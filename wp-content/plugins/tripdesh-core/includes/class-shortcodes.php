<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Front-end shortcodes: AI chat widget, hero search, and booking form.
 * Markup is deliberately plain (no framework) and progressively works
 * without JS for the search form (falls back to a normal GET request).
 */
class Tripdesh_Shortcodes {

	public function __construct() {
		add_shortcode( 'tripdesh_ai_chat', array( $this, 'render_ai_chat' ) );
		add_shortcode( 'tripdesh_search', array( $this, 'render_search' ) );
		add_shortcode( 'tripdesh_booking_form', array( $this, 'render_booking_form' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	public function enqueue_assets(): void {
		wp_register_script( 'tripdesh-shortcodes', TRIPDESH_CORE_URL . 'assets/js/shortcodes.js', array(), TRIPDESH_CORE_VERSION, true );
		wp_localize_script(
			'tripdesh-shortcodes',
			'tripdeshData',
			array(
				'restUrl' => esc_url_raw( rest_url( 'tripdesh/v1' ) ),
				'nonce'   => wp_create_nonce( 'wp_rest' ),
				'lang'    => 'bn',
				'i18n'    => array(
					'sending'        => __( 'Sending…', 'tripdesh' ),
					'thinking'       => __( 'Thinking…', 'tripdesh' ),
					'error'          => __( 'Something went wrong. Please try again.', 'tripdesh' ),
					'send'           => __( 'Send', 'tripdesh' ),
					'bookingFailed'  => __( 'Booking request failed.', 'tripdesh' ),
					'bookingNumber'  => __( 'Your booking number', 'tripdesh' ),
				),
			)
		);
	}

	public function render_ai_chat(): string {
		wp_enqueue_script( 'tripdesh-shortcodes' );
		ob_start();
		?>
		<div class="tripdesh-ai-chat" id="tripdesh-ai-chat">
			<div class="tripdesh-ai-chat__header">
				<span><?php esc_html_e( 'Tripdesh AI Travel Assistant', 'tripdesh' ); ?></span>
				<div class="tripdesh-ai-chat__lang">
					<button type="button" data-lang="bn" class="active">বাংলা</button>
					<button type="button" data-lang="en">EN</button>
				</div>
			</div>
			<div class="tripdesh-ai-chat__messages" id="tripdesh-ai-chat-messages">
				<div class="tripdesh-ai-chat__message tripdesh-ai-chat__message--bot">
					<?php esc_html_e( "Hi! Tell me your budget, number of days, and who you're travelling with, and I'll suggest destinations in Bangladesh.", 'tripdesh' ); ?>
				</div>
			</div>
			<form class="tripdesh-ai-chat__form" id="tripdesh-ai-chat-form">
				<input type="text" name="message" placeholder="<?php esc_attr_e( 'e.g. 3 days, 20,000 BDT, family from Dhaka…', 'tripdesh' ); ?>" required />
				<button type="submit"><?php esc_html_e( 'Send', 'tripdesh' ); ?></button>
			</form>
		</div>
		<?php
		return ob_get_clean();
	}

	public function render_search(): string {
		wp_enqueue_script( 'tripdesh-shortcodes' );
		$action = home_url( '/tours/' );
		ob_start();
		?>
		<form class="tripdesh-search" method="get" action="<?php echo esc_url( $action ); ?>">
			<div class="tripdesh-search__field">
				<label for="tripdesh-search-destination"><?php esc_html_e( 'Destination', 'tripdesh' ); ?></label>
				<input type="text" id="tripdesh-search-destination" name="destination" placeholder="<?php esc_attr_e( "Cox's Bazar, Sylhet…", 'tripdesh' ); ?>" />
			</div>
			<div class="tripdesh-search__field">
				<label for="tripdesh-search-date"><?php esc_html_e( 'Travel Date', 'tripdesh' ); ?></label>
				<input type="date" id="tripdesh-search-date" name="date" />
			</div>
			<div class="tripdesh-search__field">
				<label for="tripdesh-search-travelers"><?php esc_html_e( 'How many travelers?', 'tripdesh' ); ?></label>
				<input type="number" id="tripdesh-search-travelers" name="travelers" min="1" value="2" />
			</div>
			<div class="tripdesh-search__field">
				<label for="tripdesh-search-budget"><?php esc_html_e( 'Budget (BDT)', 'tripdesh' ); ?></label>
				<input type="number" id="tripdesh-search-budget" name="budget" min="0" step="1000" placeholder="30000" />
			</div>
			<div class="tripdesh-search__field">
				<label for="tripdesh-search-style"><?php esc_html_e( 'Travel Type', 'tripdesh' ); ?></label>
				<select id="tripdesh-search-style" name="travel_style">
					<option value=""><?php esc_html_e( 'Any', 'tripdesh' ); ?></option>
					<?php foreach ( get_terms( array( 'taxonomy' => 'travel_style', 'hide_empty' => false ) ) as $term ) : ?>
						<?php if ( ! is_wp_error( $term ) ) : ?>
							<option value="<?php echo esc_attr( $term->slug ); ?>"><?php echo esc_html( $term->name ); ?></option>
						<?php endif; ?>
					<?php endforeach; ?>
				</select>
			</div>
			<button type="submit" class="tripdesh-search__submit"><?php esc_html_e( 'Search Trips', 'tripdesh' ); ?></button>
		</form>
		<?php
		return ob_get_clean();
	}

	public function render_booking_form( array $atts ): string {
		$atts = shortcode_atts( array( 'id' => get_the_ID() ), $atts );
		$product_id = absint( $atts['id'] );
		if ( ! $product_id ) {
			return '';
		}

		wp_enqueue_script( 'tripdesh-shortcodes' );
		$price      = get_post_meta( $product_id, '_tripdesh_price', true );
		$sale_price = get_post_meta( $product_id, '_tripdesh_sale_price', true );
		$currency   = Tripdesh_Core::instance()->settings->get( 'currency', 'BDT' );
		$on_sale    = $sale_price && $price && (float) $sale_price < (float) $price;

		ob_start();
		?>
		<form class="tripdesh-booking-form" id="tripdesh-booking-form" data-product-id="<?php echo esc_attr( $product_id ); ?>">
			<h3><?php esc_html_e( 'Request to Book', 'tripdesh' ); ?></h3>
			<?php if ( $on_sale ) : ?>
				<p class="tripdesh-booking-form__price">
					<s class="tripdesh-booking-form__price--was"><?php echo esc_html( number_format_i18n( (float) $price ) . ' ' . $currency ); ?></s>
					<?php echo esc_html( number_format_i18n( (float) $sale_price ) . ' ' . $currency ); ?>
					<span><?php esc_html_e( 'per person', 'tripdesh' ); ?></span>
				</p>
			<?php elseif ( $price ) : ?>
				<p class="tripdesh-booking-form__price"><?php echo esc_html( number_format_i18n( (float) $price ) . ' ' . $currency ); ?> <span><?php esc_html_e( 'per person', 'tripdesh' ); ?></span></p>
			<?php endif; ?>
			<div class="tripdesh-booking-form__row">
				<label for="tripdesh-booking-name"><?php esc_html_e( 'Full Name', 'tripdesh' ); ?></label>
				<input type="text" id="tripdesh-booking-name" name="name" required />
			</div>
			<div class="tripdesh-booking-form__row">
				<label for="tripdesh-booking-phone"><?php esc_html_e( 'Phone', 'tripdesh' ); ?></label>
				<input type="tel" id="tripdesh-booking-phone" name="phone" required />
			</div>
			<div class="tripdesh-booking-form__row">
				<label for="tripdesh-booking-email"><?php esc_html_e( 'Email', 'tripdesh' ); ?></label>
				<input type="email" id="tripdesh-booking-email" name="email" required />
			</div>
			<div class="tripdesh-booking-form__row">
				<label for="tripdesh-booking-date"><?php esc_html_e( 'Travel Date', 'tripdesh' ); ?></label>
				<input type="date" id="tripdesh-booking-date" name="travel_date" required />
			</div>
			<div class="tripdesh-booking-form__row">
				<label for="tripdesh-booking-travelers"><?php esc_html_e( 'Number of Travelers', 'tripdesh' ); ?></label>
				<input type="number" id="tripdesh-booking-travelers" name="travelers" min="1" value="1" required />
			</div>
			<div class="tripdesh-booking-form__row">
				<label for="tripdesh-booking-notes"><?php esc_html_e( 'Notes (optional)', 'tripdesh' ); ?></label>
				<textarea id="tripdesh-booking-notes" name="notes" rows="3"></textarea>
			</div>
			<button type="submit"><?php esc_html_e( 'Request Booking', 'tripdesh' ); ?></button>
			<p class="tripdesh-booking-form__note"><?php esc_html_e( 'No payment is taken now. Our team will confirm availability and follow up with payment instructions.', 'tripdesh' ); ?></p>
			<div class="tripdesh-booking-form__result" id="tripdesh-booking-result" hidden></div>
		</form>
		<?php
		return ob_get_clean();
	}
}
