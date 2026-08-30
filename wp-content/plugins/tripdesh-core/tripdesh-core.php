<?php
/**
 * Plugin Name: Tripdesh Core
 * Description: Data model, booking flow, AI concierge, and SEO schema for the Tripdesh Bangladesh travel platform. Pairs with the "tripdesh" theme.
 * Version: 1.0.0
 * Requires PHP: 7.4
 * Requires at least: 5.9
 * Text Domain: tripdesh
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TRIPDESH_CORE_VERSION', '1.0.0' );
define( 'TRIPDESH_CORE_PATH', plugin_dir_path( __FILE__ ) );
define( 'TRIPDESH_CORE_URL', plugin_dir_url( __FILE__ ) );

/**
 * Tripdesh is a Bangla-first customer-facing site: the front end (including
 * REST API responses) always renders in Bengali, regardless of the site's
 * configured admin language, so wp-admin stays whatever locale the site
 * owner actually uses (usually English, "for technical usability" per the
 * brief). Registered at top-level so it's in place before WordPress loads
 * any translation file, including its own core strings.
 *
 * This only supplies Bengali for strings this theme/plugin ship a bn_BD
 * .mo file for (see languages/) — WordPress core's own UI strings (e.g.
 * "Older posts", comment form labels) need the WP core Bengali language
 * pack too; see README.md for that one manual step.
 */
function tripdesh_force_frontend_locale( $locale ) {
	if ( is_admin() ) {
		return $locale;
	}
	return 'bn_BD';
}
add_filter( 'locale', 'tripdesh_force_frontend_locale' );
add_filter( 'determine_locale', 'tripdesh_force_frontend_locale' );

require_once TRIPDESH_CORE_PATH . 'includes/class-post-types.php';
require_once TRIPDESH_CORE_PATH . 'includes/class-taxonomies.php';
require_once TRIPDESH_CORE_PATH . 'includes/class-meta-boxes.php';
require_once TRIPDESH_CORE_PATH . 'includes/class-settings.php';
require_once TRIPDESH_CORE_PATH . 'includes/class-payment-gateway.php';
require_once TRIPDESH_CORE_PATH . 'includes/class-booking.php';
require_once TRIPDESH_CORE_PATH . 'includes/class-ai-concierge.php';
require_once TRIPDESH_CORE_PATH . 'includes/class-rest-api.php';
require_once TRIPDESH_CORE_PATH . 'includes/class-seo-schema.php';
require_once TRIPDESH_CORE_PATH . 'includes/class-shortcodes.php';
require_once TRIPDESH_CORE_PATH . 'includes/class-demo-content.php';

/**
 * Bootstraps all plugin components. Each class wires its own hooks in its
 * constructor, so instantiation order only matters where one depends on
 * another (Booking depends on Payment_Gateway; REST_Api depends on Booking
 * and AI_Concierge).
 */
final class Tripdesh_Core {

	private static ?Tripdesh_Core $instance = null;

	public Tripdesh_Post_Types $post_types;
	public Tripdesh_Taxonomies $taxonomies;
	public Tripdesh_Meta_Boxes $meta_boxes;
	public Tripdesh_Settings $settings;
	public Tripdesh_Payment_Gateway $payment_gateway;
	public Tripdesh_Booking $booking;
	public Tripdesh_AI_Concierge $ai_concierge;
	public Tripdesh_REST_Api $rest_api;
	public Tripdesh_SEO_Schema $seo_schema;
	public Tripdesh_Shortcodes $shortcodes;
	public Tripdesh_Demo_Content $demo_content;

	public static function instance(): Tripdesh_Core {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		$this->post_types      = new Tripdesh_Post_Types();
		$this->taxonomies      = new Tripdesh_Taxonomies();
		$this->meta_boxes      = new Tripdesh_Meta_Boxes();
		$this->settings        = new Tripdesh_Settings();
		$this->payment_gateway = new Tripdesh_Payment_Gateway();
		$this->booking         = new Tripdesh_Booking( $this->payment_gateway );
		$this->ai_concierge    = new Tripdesh_AI_Concierge( $this->settings );
		$this->rest_api        = new Tripdesh_REST_Api( $this->booking, $this->ai_concierge );
		$this->seo_schema      = new Tripdesh_SEO_Schema();
		$this->shortcodes      = new Tripdesh_Shortcodes();
		$this->demo_content    = new Tripdesh_Demo_Content();

		add_action( 'init', array( $this, 'load_textdomain' ) );
	}

	public function load_textdomain(): void {
		load_plugin_textdomain( 'tripdesh', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}
}

Tripdesh_Core::instance();

register_activation_hook( __FILE__, 'tripdesh_core_activate' );
function tripdesh_core_activate(): void {
	// Post types/taxonomies register on 'init' via the classes above; call
	// them explicitly here too so the rewrite flush below sees the rules.
	Tripdesh_Core::instance()->post_types->register();
	Tripdesh_Core::instance()->taxonomies->register();
	Tripdesh_Taxonomies::seed_default_terms();
	flush_rewrite_rules();
}

register_deactivation_hook( __FILE__, 'tripdesh_core_deactivate' );
function tripdesh_core_deactivate(): void {
	flush_rewrite_rules();
}
