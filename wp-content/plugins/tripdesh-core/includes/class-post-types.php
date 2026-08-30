<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers every custom post type in the Tripdesh data model.
 * See ARCHITECTURE.md §3 for the field list behind each CPT.
 */
class Tripdesh_Post_Types {

	public function __construct() {
		add_action( 'init', array( $this, 'register' ) );
	}

	public function register(): void {
		$this->register_destination();
		$this->register_tour_package();
		$this->register_hotel();
		$this->register_activity();
		$this->register_transport_option();
		$this->register_booking();
		$this->register_testimonial();
	}

	private function common_supports(): array {
		return array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions' );
	}

	private function register_destination(): void {
		register_post_type(
			'destination',
			array(
				'labels'       => $this->labels( __( 'Destination', 'tripdesh' ), __( 'Destinations', 'tripdesh' ) ),
				'public'       => true,
				'show_in_rest' => true,
				'menu_icon'    => 'dashicons-location-alt',
				'has_archive'  => true,
				'rewrite'      => array( 'slug' => 'destinations', 'with_front' => false ),
				'supports'     => $this->common_supports(),
			)
		);
	}

	private function register_tour_package(): void {
		register_post_type(
			'tour_package',
			array(
				'labels'       => $this->labels( __( 'Tour Package', 'tripdesh' ), __( 'Tour Packages', 'tripdesh' ) ),
				'public'       => true,
				'show_in_rest' => true,
				'menu_icon'    => 'dashicons-palmtree',
				'has_archive'  => true,
				'rewrite'      => array( 'slug' => 'tours', 'with_front' => false ),
				'supports'     => $this->common_supports(),
			)
		);
	}

	private function register_hotel(): void {
		register_post_type(
			'hotel',
			array(
				'labels'       => $this->labels( __( 'Hotel', 'tripdesh' ), __( 'Hotels', 'tripdesh' ) ),
				'public'       => true,
				'show_in_rest' => true,
				'menu_icon'    => 'dashicons-admin-multisite',
				'has_archive'  => true,
				'rewrite'      => array( 'slug' => 'hotels', 'with_front' => false ),
				'supports'     => $this->common_supports(),
			)
		);
	}

	private function register_activity(): void {
		register_post_type(
			'activity',
			array(
				'labels'       => $this->labels( __( 'Activity', 'tripdesh' ), __( 'Activities', 'tripdesh' ) ),
				'public'       => true,
				'show_in_rest' => true,
				'menu_icon'    => 'dashicons-universal-access',
				'has_archive'  => true,
				'rewrite'      => array( 'slug' => 'activities', 'with_front' => false ),
				'supports'     => $this->common_supports(),
			)
		);
	}

	private function register_transport_option(): void {
		register_post_type(
			'transport_option',
			array(
				'labels'       => $this->labels( __( 'Transport Option', 'tripdesh' ), __( 'Transportation', 'tripdesh' ) ),
				'public'       => true,
				'show_in_rest' => true,
				'menu_icon'    => 'dashicons-car',
				'has_archive'  => true,
				'rewrite'      => array( 'slug' => 'transportation', 'with_front' => false ),
				'supports'     => $this->common_supports(),
			)
		);
	}

	private function register_booking(): void {
		register_post_type(
			'tripdesh_booking',
			array(
				'labels'              => $this->labels( __( 'Booking', 'tripdesh' ), __( 'Bookings', 'tripdesh' ) ),
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_rest'        => false,
				'menu_icon'           => 'dashicons-tickets-alt',
				'has_archive'         => false,
				'exclude_from_search' => true,
				'capability_type'     => 'page',
				'supports'            => array( 'title' ),
			)
		);
	}

	private function register_testimonial(): void {
		register_post_type(
			'testimonial',
			array(
				'labels'       => $this->labels( __( 'Testimonial', 'tripdesh' ), __( 'Testimonials', 'tripdesh' ) ),
				'public'       => true,
				'show_in_rest' => true,
				'menu_icon'    => 'dashicons-format-quote',
				'has_archive'  => false,
				'rewrite'      => array( 'slug' => 'testimonials', 'with_front' => false ),
				'supports'     => array( 'title', 'editor', 'thumbnail' ),
			)
		);
	}

	private function labels( string $singular, string $plural ): array {
		return array(
			'name'               => $plural,
			'singular_name'      => $singular,
			/* translators: %s: plural label */
			'add_new_item'       => sprintf( __( 'Add New %s', 'tripdesh' ), $singular ),
			/* translators: %s: singular label */
			'edit_item'          => sprintf( __( 'Edit %s', 'tripdesh' ), $singular ),
			/* translators: %s: singular label */
			'view_item'          => sprintf( __( 'View %s', 'tripdesh' ), $singular ),
			/* translators: %s: plural label */
			'search_items'       => sprintf( __( 'Search %s', 'tripdesh' ), $plural ),
			/* translators: %s: plural label */
			'not_found'          => sprintf( __( 'No %s found', 'tripdesh' ), strtolower( $plural ) ),
			'menu_name'          => $plural,
		);
	}
}
