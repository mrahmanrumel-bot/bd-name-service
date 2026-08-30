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
	 */
	public static function seed_default_terms(): void {
		$tiers = array(
			'budget'   => __( 'Budget', 'tripdesh' ),
			'standard' => __( 'Standard', 'tripdesh' ),
			'premium'  => __( 'Premium', 'tripdesh' ),
			'luxury'   => __( 'Luxury', 'tripdesh' ),
		);
		foreach ( $tiers as $slug => $name ) {
			if ( ! term_exists( $slug, 'tour_type' ) ) {
				wp_insert_term( $name, 'tour_type', array( 'slug' => $slug ) );
			}
		}

		$styles = array(
			'family'    => __( 'Family', 'tripdesh' ),
			'couple'    => __( 'Couple', 'tripdesh' ),
			'solo'      => __( 'Solo', 'tripdesh' ),
			'group'     => __( 'Group', 'tripdesh' ),
			'adventure' => __( 'Adventure', 'tripdesh' ),
			'weekend'   => __( 'Weekend', 'tripdesh' ),
		);
		foreach ( $styles as $slug => $name ) {
			if ( ! term_exists( $slug, 'travel_style' ) ) {
				wp_insert_term( $name, 'travel_style', array( 'slug' => $slug ) );
			}
		}
	}
}
