<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings > Tripdesh admin page. Holds AI provider config, payment
 * gateway placeholders (see class-payment-gateway.php), and general
 * business info. Any option can be overridden by defining a same-named
 * PHP constant in wp-config.php (recommended for secrets in production),
 * checked first by get().
 */
class Tripdesh_Settings {

	const OPTION_GROUP = 'tripdesh_settings';
	const OPTION_NAME  = 'tripdesh_options';

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	public function add_settings_page(): void {
		add_options_page(
			__( 'Tripdesh Settings', 'tripdesh' ),
			__( 'Tripdesh', 'tripdesh' ),
			'manage_options',
			'tripdesh-settings',
			array( $this, 'render_page' )
		);
	}

	public function register_settings(): void {
		register_setting( self::OPTION_GROUP, self::OPTION_NAME, array( $this, 'sanitize' ) );
	}

	public function sanitize( array $input ): array {
		$clean = array();

		$clean['ai_provider']      = in_array( $input['ai_provider'] ?? '', array( 'anthropic', 'openai' ), true ) ? $input['ai_provider'] : 'anthropic';
		$clean['ai_api_key']       = isset( $input['ai_api_key'] ) ? sanitize_text_field( $input['ai_api_key'] ) : '';
		$clean['ai_model']         = isset( $input['ai_model'] ) ? sanitize_text_field( $input['ai_model'] ) : '';
		$clean['currency']         = isset( $input['currency'] ) ? sanitize_text_field( $input['currency'] ) : 'BDT';
		$clean['contact_phone']    = isset( $input['contact_phone'] ) ? sanitize_text_field( $input['contact_phone'] ) : '';
		$clean['contact_email']    = isset( $input['contact_email'] ) ? sanitize_email( $input['contact_email'] ) : '';
		$clean['contact_whatsapp'] = isset( $input['contact_whatsapp'] ) ? sanitize_text_field( $input['contact_whatsapp'] ) : '';
		$clean['payment_gateway']  = in_array( $input['payment_gateway'] ?? '', array( 'none', 'sslcommerz', 'bkash' ), true ) ? $input['payment_gateway'] : 'none';
		$clean['sslcommerz_store_id']  = isset( $input['sslcommerz_store_id'] ) ? sanitize_text_field( $input['sslcommerz_store_id'] ) : '';
		$clean['sslcommerz_store_password'] = isset( $input['sslcommerz_store_password'] ) ? sanitize_text_field( $input['sslcommerz_store_password'] ) : '';
		$clean['sslcommerz_sandbox']   = ! empty( $input['sslcommerz_sandbox'] );

		return $clean;
	}

	/**
	 * Reads a setting. A defined constant named TRIPDESH_{KEY_UPPERCASE}
	 * always wins over the stored option, so secrets can live outside the
	 * database in production (e.g. define('TRIPDESH_AI_API_KEY', '...')
	 * in wp-config.php).
	 */
	public function get( string $key, $default = '' ) {
		$constant = 'TRIPDESH_' . strtoupper( $key );
		if ( defined( $constant ) ) {
			return constant( $constant );
		}
		$options = get_option( self::OPTION_NAME, array() );
		return $options[ $key ] ?? $default;
	}

	public function render_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Tripdesh Settings', 'tripdesh' ); ?></h1>
			<form method="post" action="options.php">
				<?php settings_fields( self::OPTION_GROUP ); ?>
				<h2><?php esc_html_e( 'AI Concierge', 'tripdesh' ); ?></h2>
				<p class="description"><?php esc_html_e( 'Leave the API key blank to keep the AI concierge in fallback mode (a friendly bilingual message instead of live recommendations). The key is used server-side only and never sent to the browser.', 'tripdesh' ); ?></p>
				<table class="form-table">
					<tr>
						<th><label for="ai_provider"><?php esc_html_e( 'Provider', 'tripdesh' ); ?></label></th>
						<td>
							<select id="ai_provider" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[ai_provider]">
								<option value="anthropic" <?php selected( $this->get( 'ai_provider', 'anthropic' ), 'anthropic' ); ?>>Anthropic (Claude)</option>
								<option value="openai" <?php selected( $this->get( 'ai_provider' ), 'openai' ); ?>>OpenAI (GPT)</option>
							</select>
						</td>
					</tr>
					<tr>
						<th><label for="ai_api_key"><?php esc_html_e( 'API Key', 'tripdesh' ); ?></label></th>
						<td><input type="password" autocomplete="off" id="ai_api_key" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[ai_api_key]" value="<?php echo esc_attr( $this->get( 'ai_api_key' ) ); ?>" class="regular-text" /></td>
					</tr>
					<tr>
						<th><label for="ai_model"><?php esc_html_e( 'Model', 'tripdesh' ); ?></label></th>
						<td><input type="text" id="ai_model" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[ai_model]" value="<?php echo esc_attr( $this->get( 'ai_model' ) ); ?>" class="regular-text" placeholder="claude-sonnet-5" /></td>
					</tr>
				</table>

				<h2><?php esc_html_e( 'Business Info', 'tripdesh' ); ?></h2>
				<table class="form-table">
					<tr>
						<th><label for="currency"><?php esc_html_e( 'Currency', 'tripdesh' ); ?></label></th>
						<td><input type="text" id="currency" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[currency]" value="<?php echo esc_attr( $this->get( 'currency', 'BDT' ) ); ?>" class="small-text" /></td>
					</tr>
					<tr>
						<th><label for="contact_phone"><?php esc_html_e( 'Contact Phone', 'tripdesh' ); ?></label></th>
						<td><input type="text" id="contact_phone" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[contact_phone]" value="<?php echo esc_attr( $this->get( 'contact_phone' ) ); ?>" class="regular-text" /></td>
					</tr>
					<tr>
						<th><label for="contact_email"><?php esc_html_e( 'Contact Email', 'tripdesh' ); ?></label></th>
						<td><input type="email" id="contact_email" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[contact_email]" value="<?php echo esc_attr( $this->get( 'contact_email' ) ); ?>" class="regular-text" /></td>
					</tr>
					<tr>
						<th><label for="contact_whatsapp"><?php esc_html_e( 'WhatsApp Number', 'tripdesh' ); ?></label></th>
						<td><input type="text" id="contact_whatsapp" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[contact_whatsapp]" value="<?php echo esc_attr( $this->get( 'contact_whatsapp' ) ); ?>" class="regular-text" /></td>
					</tr>
				</table>

				<h2><?php esc_html_e( 'Payment Gateway (Phase 2)', 'tripdesh' ); ?></h2>
				<p class="description"><?php esc_html_e( 'Bookings are captured without payment until a gateway is implemented against these settings. See ARCHITECTURE.md.', 'tripdesh' ); ?></p>
				<table class="form-table">
					<tr>
						<th><label for="payment_gateway"><?php esc_html_e( 'Gateway', 'tripdesh' ); ?></label></th>
						<td>
							<select id="payment_gateway" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[payment_gateway]">
								<option value="none" <?php selected( $this->get( 'payment_gateway', 'none' ), 'none' ); ?>><?php esc_html_e( 'None (manual confirmation)', 'tripdesh' ); ?></option>
								<option value="sslcommerz" <?php selected( $this->get( 'payment_gateway' ), 'sslcommerz' ); ?>>SSLCommerz (bKash/Nagad/Rocket/Cards)</option>
								<option value="bkash" <?php selected( $this->get( 'payment_gateway' ), 'bkash' ); ?>>bKash Merchant API</option>
							</select>
						</td>
					</tr>
					<tr>
						<th><label for="sslcommerz_store_id"><?php esc_html_e( 'SSLCommerz Store ID', 'tripdesh' ); ?></label></th>
						<td><input type="text" id="sslcommerz_store_id" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[sslcommerz_store_id]" value="<?php echo esc_attr( $this->get( 'sslcommerz_store_id' ) ); ?>" class="regular-text" /></td>
					</tr>
					<tr>
						<th><label for="sslcommerz_store_password"><?php esc_html_e( 'SSLCommerz Store Password', 'tripdesh' ); ?></label></th>
						<td><input type="password" autocomplete="off" id="sslcommerz_store_password" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[sslcommerz_store_password]" value="<?php echo esc_attr( $this->get( 'sslcommerz_store_password' ) ); ?>" class="regular-text" /></td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Sandbox Mode', 'tripdesh' ); ?></th>
						<td><label><input type="checkbox" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[sslcommerz_sandbox]" value="1" <?php checked( $this->get( 'sslcommerz_sandbox' ) ); ?> /> <?php esc_html_e( 'Use sandbox/test credentials', 'tripdesh' ); ?></label></td>
					</tr>
				</table>

				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
}
