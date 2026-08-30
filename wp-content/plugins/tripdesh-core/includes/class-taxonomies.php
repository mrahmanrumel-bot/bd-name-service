<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shared taxonomies used to cross-link tours/hotels/activities/transport
 * back to a destination, and to tier tour packages.
 */
class Tripdesh_Taxonomies {

	public function __construct() {
		add_action( 'init', array( $this, 'register' ) );
	}

	public function register(): void {
		register_taxonomy(
			'tripdesh_location',
			array( 'tour_package', 'hotel', 'activity', 'transport_option', 'post' ),
			array(
				'labels'       => array(
					'name'          => __( 'Locations', 'tripdesh' ),
					'singular_name' => __( 'Location', 'tripdesh' ),
				),
				'public'       => true,
				'hierarchical' => true,
				'show_in_rest' => true,
				'rewrite'      => array( 'slug' => 'location', 'with_front' => false ),
			)
		);

		register_taxonomy(
			'tour_type',
			array( 'tour_package' ),
			array(
				'labels'       => array(
					'name'          => __( 'Tour Tiers', 'tripdesh' ),
					'singular_name' => __( 'Tour Tier', 'tripdesh' ),
				),
				'public'       => true,
				'hierarchical' => true,
				'show_in_rest' => true,
				'rewrite'      => array( 'slug' => 'tour-type', 'with_front' => false ),
			)
		);

		register_taxonomy(
			'travel_style',
			array( 'tour_package' ),
			array(
				'labels'       => array(
					'name'          => __( 'Travel Styles', 'tripdesh' ),
					'singular_name' => __( 'Travel Style', 'tripdesh' ),
				),
				'public'       => true,
				'hierarchical' => false,
				'show_in_rest' => true,
				'rewrite'      => array( 'slug' => 'travel-style', 'with_front' => false ),
			)
		);
	}

	/**
	 * Seeds the tour_type taxonomy with the four tiers from the brief, if
	 * they don't already exist. Safe to call multiple times.
	 *
	 * Term names are written directly in Bengali (not run through __())
	 * because taxonomy terms are stored content, not UI chrome — a __()
	 * call here would only capture whatever locale was active at the
	 * moment the term was inserted (usually wp-admin's locale), not
	 * translate dynamically per visitor the way template strings do.
	 * Slugs stay in English for clean, stable URLs.
	 */
	public static function seed_default_terms(): void {
		$tiers = array(
			'budget'   => 'বাজেট',
			'standard' => 'স্ট্যান্ডার্ড',
			'premium'  => 'প্রিমিয়াম',
			'luxury'   => 'লাক্সারি',
		);
		foreach ( $tiers as $slug => $name ) {
			if ( ! term_exists( $slug, 'tour_type' ) ) {
				wp_insert_term( $name, 'tour_type', array( 'slug' => $slug ) );
			}
		}

		$styles = array(
			'family'    => 'পারিবারিক',
			'couple'    => 'কাপল',
			'solo'      => 'একক ভ্রমণ',
			'group'     => 'গ্রুপ',
			'adventure' => 'অ্যাডভেঞ্চার',
			'weekend'   => 'উইকএন্ড',
		);
		foreach ( $styles as $slug => $name ) {
			if ( ! term_exists( $slug, 'travel_style' ) ) {
				wp_insert_term( $name, 'travel_style', array( 'slug' => $slug ) );
			}
		}
	}
}
