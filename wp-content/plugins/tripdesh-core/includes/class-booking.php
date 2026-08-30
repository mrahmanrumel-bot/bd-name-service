<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Booking capture and lifecycle. Creates a tripdesh_booking post per
 * request, generates a human-readable reference, and notifies both
 * customer and admin by email. No payment is captured here — see
 * class-payment-gateway.php and ARCHITECTURE.md §5.
 */
class Tripdesh_Booking {

	const STATUS_PENDING             = 'pending';
	const STATUS_AWAITING_PAYMENT    = 'awaiting_payment';
	const STATUS_CONFIRMED           = 'confirmed';
	const STATUS_CANCELLED           = 'cancelled';

	private Tripdesh_Payment_Gateway $gateway;

	public function __construct( Tripdesh_Payment_Gateway $gateway ) {
		$this->gateway = $gateway;

		add_action( 'add_meta_boxes', array( $this, 'add_status_box' ) );
		add_action( 'save_post_tripdesh_booking', array( $this, 'save_status' ) );
	}

	/**
	 * Validates input, creates the booking post, and returns a result
	 * array ready to be sent as a REST response. Throws WP_Error-wrapped
	 * data via array 'error' key rather than exceptions, since this is
	 * called directly from the REST controller.
	 */
	public function create( array $data ): array {
		$product_id = isset( $data['product_id'] ) ? absint( $data['product_id'] ) : 0;
		$product    = $product_id ? get_post( $product_id ) : null;

		if ( ! $product || ! in_array( $product->post_type, array( 'tour_package', 'hotel', 'activity' ), true ) || 'publish' !== $product->post_status ) {
			return array( 'error' => __( 'The selected tour, hotel, or activity could not be found.', 'tripdesh' ) );
		}

		$name  = isset( $data['name'] ) ? sanitize_text_field( $data['name'] ) : '';
		$phone = isset( $data['phone'] ) ? sanitize_text_field( $data['phone'] ) : '';
		$email = isset( $data['email'] ) ? sanitize_email( $data['email'] ) : '';

		if ( '' === $name || '' === $phone || ! is_email( $email ) ) {
			return array( 'error' => __( 'Please provide your name, a valid phone number, and a valid email address.', 'tripdesh' ) );
		}

		$travelers   = isset( $data['travelers'] ) ? max( 1, absint( $data['travelers'] ) ) : 1;
		$travel_date = isset( $data['travel_date'] ) ? sanitize_text_field( $data['travel_date'] ) : '';
		$notes       = isset( $data['notes'] ) ? sanitize_textarea_field( $data['notes'] ) : '';

		$list_price = (float) get_post_meta( $product_id, '_tripdesh_price', true );
		$sale_price = (float) get_post_meta( $product_id, '_tripdesh_sale_price', true );
		$unit_price = ( $sale_price > 0 && $sale_price < $list_price ) ? $sale_price : $list_price;

		$total_price = $unit_price * $travelers;

		$reference = $this->generate_reference();

		$booking_id = wp_insert_post(
			array(
				'post_type'   => 'tripdesh_booking',
				'post_title'  => $reference,
				'post_status' => 'private',
			),
			true
		);

		if ( is_wp_error( $booking_id ) ) {
			return array( 'error' => __( 'Could not create the booking. Please try again.', 'tripdesh' ) );
		}

		update_post_meta( $booking_id, '_tripdesh_reference', $reference );
		update_post_meta( $booking_id, '_tripdesh_product_id', $product_id );
		update_post_meta( $booking_id, '_tripdesh_product_type', $product->post_type );
		update_post_meta( $booking_id, '_tripdesh_customer_name', $name );
		update_post_meta( $booking_id, '_tripdesh_customer_phone', $phone );
		update_post_meta( $booking_id, '_tripdesh_customer_email', $email );
		update_post_meta( $booking_id, '_tripdesh_travelers', $travelers );
		update_post_meta( $booking_id, '_tripdesh_travel_date', $travel_date );
		update_post_meta( $booking_id, '_tripdesh_notes', $notes );
		update_post_meta( $booking_id, '_tripdesh_unit_price', $unit_price );
		update_post_meta( $booking_id, '_tripdesh_total_price', $total_price );
		update_post_meta( $booking_id, '_tripdesh_status', self::STATUS_PENDING );

		$payment_url = $this->gateway->create_payment_session( $booking_id );
		if ( $payment_url ) {
			update_post_meta( $booking_id, '_tripdesh_status', self::STATUS_AWAITING_PAYMENT );
		}

		$this->notify_customer( $booking_id );
		$this->notify_admin( $booking_id );

		return array(
			'success'     => true,
			'reference'   => $reference,
			'total_price' => $total_price,
			'currency'    => Tripdesh_Core::instance()->settings->get( 'currency', 'BDT' ),
			'payment_url' => $payment_url,
			'message'     => __( 'Your request has been received. Our team will confirm availability and contact you shortly.', 'tripdesh' ),
		);
	}

	/**
	 * BDT-{year}-{6 digit sequence}, e.g. BDT-2026-000123 (brief §8).
	 * The sequence is a simple incrementing option counter — adequate at
	 * this traffic scale; move to a DB sequence/UUID if volume grows.
	 */
	private function generate_reference(): string {
		$year        = gmdate( 'Y' );
		$option_key  = 'tripdesh_booking_seq_' . $year;
		$next        = (int) get_option( $option_key, 0 ) + 1;
		update_option( $option_key, $next, false );
		return sprintf( 'BDT-%s-%06d', $year, $next );
	}

	private function notify_customer( int $booking_id ): void {
		$email     = get_post_meta( $booking_id, '_tripdesh_customer_email', true );
		$reference = get_post_meta( $booking_id, '_tripdesh_reference', true );
		$name      = get_post_meta( $booking_id, '_tripdesh_customer_name', true );

		/* translators: %s: booking reference */
		$subject = sprintf( __( 'Tripdesh booking request received — %s', 'tripdesh' ), $reference );
		$body    = sprintf(
			/* translators: 1: customer name, 2: booking reference */
			__( "Hi %1\$s,\n\nThanks for your booking request. Your reference is %2\$s.\n\nOur team will confirm availability and reach out to arrange payment. If you have questions, reply to this email.\n\n— Tripdesh", 'tripdesh' ),
			$name,
			$reference
		);

		wp_mail( $email, $subject, $body );
	}

	private function notify_admin( int $booking_id ): void {
		$admin_email = Tripdesh_Core::instance()->settings->get( 'contact_email', get_option( 'admin_email' ) );
		$reference   = get_post_meta( $booking_id, '_tripdesh_reference', true );
		$edit_link   = admin_url( 'post.php?post=' . $booking_id . '&action=edit' );

		/* translators: %s: booking reference */
		$subject = sprintf( __( 'New booking request — %s', 'tripdesh' ), $reference );
		$body    = sprintf(
			/* translators: %s: admin edit link */
			__( "A new booking request has come in.\n\nReview it here: %s", 'tripdesh' ),
			$edit_link
		);

		wp_mail( $admin_email, $subject, $body );
	}

	public function add_status_box(): void {
		add_meta_box(
			'tripdesh_booking_status',
			__( 'Booking Status', 'tripdesh' ),
			array( $this, 'render_status_box' ),
			'tripdesh_booking',
			'side',
			'high'
		);
		add_meta_box(
			'tripdesh_booking_summary',
			__( 'Booking Summary', 'tripdesh' ),
			array( $this, 'render_summary_box' ),
			'tripdesh_booking',
			'normal',
			'high'
		);
	}

	public function render_status_box( WP_Post $post ): void {
		wp_nonce_field( 'tripdesh_booking_status', 'tripdesh_booking_status_nonce' );
		$status = get_post_meta( $post->ID, '_tripdesh_status', true );
		$options = array(
			self::STATUS_PENDING          => __( 'Pending', 'tripdesh' ),
			self::STATUS_AWAITING_PAYMENT => __( 'Awaiting Payment', 'tripdesh' ),
			self::STATUS_CONFIRMED        => __( 'Confirmed', 'tripdesh' ),
			self::STATUS_CANCELLED        => __( 'Cancelled', 'tripdesh' ),
		);
		echo '<select name="tripdesh_booking_status" style="width:100%">';
		foreach ( $options as $value => $label ) {
			echo '<option value="' . esc_attr( $value ) . '" ' . selected( $status, $value, false ) . '>' . esc_html( $label ) . '</option>';
		}
		echo '</select>';
		echo '<p class="description">' . esc_html__( 'Confirm manually once payment is verified. Automated status transitions arrive with the Phase 2 payment gateway.', 'tripdesh' ) . '</p>';
	}

	public function render_summary_box( WP_Post $post ): void {
		$fields = array(
			'reference'      => __( 'Reference', 'tripdesh' ),
			'customer_name'  => __( 'Customer', 'tripdesh' ),
			'customer_phone' => __( 'Phone', 'tripdesh' ),
			'customer_email' => __( 'Email', 'tripdesh' ),
			'travelers'      => __( 'Travelers', 'tripdesh' ),
			'travel_date'    => __( 'Travel Date', 'tripdesh' ),
			'total_price'    => __( 'Total Price', 'tripdesh' ),
			'notes'          => __( 'Notes', 'tripdesh' ),
		);
		$product_id = get_post_meta( $post->ID, '_tripdesh_product_id', true );

		echo '<table class="widefat"><tbody>';
		if ( $product_id ) {
			echo '<tr><th>' . esc_html__( 'Product', 'tripdesh' ) . '</th><td><a href="' . esc_url( get_edit_post_link( (int) $product_id ) ) . '">' . esc_html( get_the_title( (int) $product_id ) ) . '</a></td></tr>';
		}
		foreach ( $fields as $key => $label ) {
			$value = get_post_meta( $post->ID, '_tripdesh_' . $key, true );
			echo '<tr><th>' . esc_html( $label ) . '</th><td>' . esc_html( $value ) . '</td></tr>';
		}
		echo '</tbody></table>';
	}

	public function save_status( int $post_id ): void {
		if ( ! isset( $_POST['tripdesh_booking_status_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tripdesh_booking_status_nonce'] ) ), 'tripdesh_booking_status' ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		if ( ! isset( $_POST['tripdesh_booking_status'] ) ) {
			return;
		}
		$status = sanitize_text_field( wp_unslash( $_POST['tripdesh_booking_status'] ) );
		$valid  = array( self::STATUS_PENDING, self::STATUS_AWAITING_PAYMENT, self::STATUS_CONFIRMED, self::STATUS_CANCELLED );
		if ( in_array( $status, $valid, true ) ) {
			update_post_meta( $post_id, '_tripdesh_status', $status );
		}
	}
}
