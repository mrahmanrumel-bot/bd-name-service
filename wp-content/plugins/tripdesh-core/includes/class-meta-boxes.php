<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin meta boxes + save handling for every CPT's custom fields.
 * All meta keys are prefixed `_tripdesh_` and registered with
 * show_in_rest so the block editor / REST API can read them too.
 */
class Tripdesh_Meta_Boxes {

	/**
	 * post_type => [ field_key => [ label, type ] ]
	 * type is one of: text, number, textarea, url, email, select
	 */
	private array $fields;

	public function __construct() {
		$this->fields = $this->field_map();

		add_action( 'init', array( $this, 'register_meta' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save' ) );
	}

	private function field_map(): array {
		return array(
			'destination'       => array(
				'best_time_to_visit' => array( __( 'Best Time to Visit', 'tripdesh' ), 'text' ),
				'how_to_reach'        => array( __( 'How to Reach', 'tripdesh' ), 'textarea' ),
				'where_to_stay'       => array( __( 'Where to Stay', 'tripdesh' ), 'textarea' ),
				'things_to_do'        => array( __( 'Things to Do', 'tripdesh' ), 'textarea' ),
				'food'                => array( __( 'Food', 'tripdesh' ), 'textarea' ),
				'estimated_budget'    => array( __( 'Estimated Budget (BDT, per person)', 'tripdesh' ), 'text' ),
				'safety_info'         => array( __( 'Safety Information', 'tripdesh' ), 'textarea' ),
				'local_transport'     => array( __( 'Local Transportation', 'tripdesh' ), 'textarea' ),
				'recommended_days'    => array( __( 'Recommended Trip Length (days)', 'tripdesh' ), 'number' ),
				'featured_collection' => array( __( 'Featured Collection Key (e.g. tea_garden)', 'tripdesh' ), 'text' ),
			),
			'tour_package'       => array(
				'duration_days'      => array( __( 'Duration (days)', 'tripdesh' ), 'number' ),
				'duration_nights'    => array( __( 'Duration (nights)', 'tripdesh' ), 'number' ),
				'price'              => array( __( 'Customer Price (BDT, per person)', 'tripdesh' ), 'number' ),
				'sale_price'         => array( __( 'Deal / Sale Price (BDT, optional — leave blank if not on offer)', 'tripdesh' ), 'number' ),
				'supplier_price'     => array( __( 'Supplier Price (BDT, internal)', 'tripdesh' ), 'number' ),
				'departure_location' => array( __( 'Departure Location', 'tripdesh' ), 'text' ),
				'max_travelers'      => array( __( 'Max Travelers per Booking', 'tripdesh' ), 'number' ),
				'inclusions'         => array( __( 'Inclusions', 'tripdesh' ), 'textarea' ),
				'exclusions'         => array( __( 'Exclusions', 'tripdesh' ), 'textarea' ),
				'itinerary'          => array( __( 'Day-by-Day Itinerary', 'tripdesh' ), 'itinerary' ),
				'terms'              => array( __( 'Terms & Conditions', 'tripdesh' ), 'textarea' ),
				'availability'       => array( __( 'Availability Notes', 'tripdesh' ), 'text' ),
			),
			'hotel'              => array(
				'star_rating'     => array( __( 'Star Rating (1-5)', 'tripdesh' ), 'number' ),
				'price_per_night' => array( __( 'Price per Night (BDT)', 'tripdesh' ), 'number' ),
				'room_types'      => array( __( 'Room Types', 'tripdesh' ), 'textarea' ),
				'amenities'       => array( __( 'Amenities', 'tripdesh' ), 'textarea' ),
				'policies'        => array( __( 'Policies (check-in/out, cancellation)', 'tripdesh' ), 'textarea' ),
				'address'         => array( __( 'Address', 'tripdesh' ), 'text' ),
				'phone'           => array( __( 'Contact Phone', 'tripdesh' ), 'text' ),
			),
			'activity'           => array(
				'price'    => array( __( 'Price (BDT, per person)', 'tripdesh' ), 'number' ),
				'duration' => array( __( 'Duration', 'tripdesh' ), 'text' ),
				'includes' => array( __( 'What\'s Included', 'tripdesh' ), 'textarea' ),
			),
			'transport_option'   => array(
				'mode'        => array( __( 'Mode (bus/train/car/microbus/boat/launch)', 'tripdesh' ), 'text' ),
				'from'        => array( __( 'From', 'tripdesh' ), 'text' ),
				'to'          => array( __( 'To', 'tripdesh' ), 'text' ),
				'price'       => array( __( 'Price (BDT)', 'tripdesh' ), 'number' ),
				'operator'    => array( __( 'Operator', 'tripdesh' ), 'text' ),
			),
			'testimonial'        => array(
				'customer_name' => array( __( 'Customer Name', 'tripdesh' ), 'text' ),
				'rating'        => array( __( 'Rating (1-5)', 'tripdesh' ), 'number' ),
				'trip_taken'    => array( __( 'Trip Taken', 'tripdesh' ), 'text' ),
			),
		);
	}

	public function register_meta(): void {
		foreach ( $this->fields as $post_type => $fields ) {
			foreach ( $fields as $key => $definition ) {
				register_post_meta(
					$post_type,
					'_tripdesh_' . $key,
					array(
						'type'         => 'itinerary' === $definition[1] ? 'string' : ( 'number' === $definition[1] ? 'number' : 'string' ),
						'single'       => true,
						'show_in_rest' => true,
						'auth_callback' => function () {
							return current_user_can( 'edit_posts' );
						},
					)
				);
			}
		}
	}

	public function add_meta_boxes(): void {
		foreach ( $this->fields as $post_type => $fields ) {
			add_meta_box(
				'tripdesh_' . $post_type . '_details',
				__( 'Tripdesh Details', 'tripdesh' ),
				array( $this, 'render' ),
				$post_type,
				'normal',
				'high'
			);
		}
	}

	public function render( WP_Post $post ): void {
		if ( ! isset( $this->fields[ $post->post_type ] ) ) {
			return;
		}

		wp_nonce_field( 'tripdesh_save_meta', 'tripdesh_meta_nonce' );

		echo '<table class="form-table"><tbody>';
		foreach ( $this->fields[ $post->post_type ] as $key => $definition ) {
			list( $label, $type ) = $definition;
			$meta_key             = '_tripdesh_' . $key;
			$value                = get_post_meta( $post->ID, $meta_key, true );
			$field_id             = 'tripdesh_field_' . $key;

			echo '<tr><th><label for="' . esc_attr( $field_id ) . '">' . esc_html( $label ) . '</label></th><td>';

			switch ( $type ) {
				case 'textarea':
					echo '<textarea id="' . esc_attr( $field_id ) . '" name="' . esc_attr( $meta_key ) . '" rows="4" class="large-text">' . esc_textarea( $value ) . '</textarea>';
					break;
				case 'number':
					echo '<input type="number" step="any" id="' . esc_attr( $field_id ) . '" name="' . esc_attr( $meta_key ) . '" value="' . esc_attr( $value ) . '" class="regular-text" />';
					break;
				case 'itinerary':
					echo '<textarea id="' . esc_attr( $field_id ) . '" name="' . esc_attr( $meta_key ) . '" rows="8" class="large-text code" placeholder="Day 1: Dhaka to Cox\'s Bazar - hotel check-in, beach walk&#10;Day 2: Himchari, Inani Beach, Marine Drive&#10;Day 3: Local sightseeing, return to Dhaka">' . esc_textarea( $value ) . '</textarea>';
					echo '<p class="description">' . esc_html__( 'One line per day: "Day N: description". Rendered as a numbered itinerary on the front end.', 'tripdesh' ) . '</p>';
					break;
				default:
					echo '<input type="text" id="' . esc_attr( $field_id ) . '" name="' . esc_attr( $meta_key ) . '" value="' . esc_attr( $value ) . '" class="regular-text" />';
			}

			echo '</td></tr>';
		}
		echo '</tbody></table>';
	}

	public function save( int $post_id ): void {
		if ( ! isset( $_POST['tripdesh_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tripdesh_meta_nonce'] ) ), 'tripdesh_save_meta' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$post_type = get_post_type( $post_id );
		if ( ! isset( $this->fields[ $post_type ] ) ) {
			return;
		}

		foreach ( $this->fields[ $post_type ] as $key => $definition ) {
			$meta_key = '_tripdesh_' . $key;
			if ( ! isset( $_POST[ $meta_key ] ) ) {
				continue;
			}
			$type = $definition[1];
			$raw  = wp_unslash( $_POST[ $meta_key ] );

			if ( in_array( $type, array( 'textarea', 'itinerary' ), true ) ) {
				$value = sanitize_textarea_field( $raw );
			} elseif ( 'number' === $type ) {
				$value = is_numeric( $raw ) ? $raw + 0 : '';
			} else {
				$value = sanitize_text_field( $raw );
			}

			update_post_meta( $post_id, $meta_key, $value );
		}
	}

	/**
	 * Parses the itinerary textarea format ("Day N: ...") into a structured
	 * array for template rendering.
	 */
	public static function parse_itinerary( string $raw ): array {
		$days = array();
		foreach ( preg_split( '/\r\n|\r|\n/', $raw ) as $line ) {
			$line = trim( $line );
			if ( '' === $line ) {
				continue;
			}
			if ( preg_match( '/^Day\s*(\d+)\s*:\s*(.+)$/i', $line, $matches ) ) {
				$days[] = array(
					'day'         => (int) $matches[1],
					'description' => $matches[2],
				);
			} else {
				$days[] = array(
					'day'         => count( $days ) + 1,
					'description' => $line,
				);
			}
		}
		return $days;
	}
}
