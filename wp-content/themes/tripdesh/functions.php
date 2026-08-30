<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TRIPDESH_THEME_VERSION', '1.0.0' );

require_once get_template_directory() . '/inc/template-tags.php';

function tripdesh_setup(): void {
	load_theme_textdomain( 'tripdesh', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'align-wide' );

	add_image_size( 'tripdesh-card', 480, 320, true );
	add_image_size( 'tripdesh-hero', 1600, 900, true );

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'tripdesh' ),
			'footer'  => __( 'Footer Menu', 'tripdesh' ),
		)
	);
}
add_action( 'after_setup_theme', 'tripdesh_setup' );

function tripdesh_enqueue_assets(): void {
	wp_enqueue_style( 'tripdesh-main', get_template_directory_uri() . '/assets/css/main.css', array(), TRIPDESH_THEME_VERSION );
	wp_enqueue_script( 'tripdesh-nav', get_template_directory_uri() . '/assets/js/main.js', array(), TRIPDESH_THEME_VERSION, true );

	if ( is_singular() && comments_open() ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'tripdesh_enqueue_assets' );

function tripdesh_widgets_init(): void {
	register_sidebar(
		array(
			'name'          => __( 'Footer Column 1', 'tripdesh' ),
			'id'            => 'footer-1',
			'before_widget' => '<div class="tripdesh-footer-widget">',
			'after_widget'  => '</div>',
			'before_title'  => '<h4>',
			'after_title'   => '</h4>',
		)
	);
	register_sidebar(
		array(
			'name'          => __( 'Blog Sidebar', 'tripdesh' ),
			'id'            => 'blog-sidebar',
			'before_widget' => '<div class="tripdesh-widget">',
			'after_widget'  => '</div>',
			'before_title'  => '<h4>',
			'after_title'   => '</h4>',
		)
	);
}
add_action( 'widgets_init', 'tripdesh_widgets_init' );

/**
 * Breadcrumbs (brief §17). Plain output, no schema here — JSON-LD
 * BreadcrumbList is handled in the plugin's SEO class when active.
 */
function tripdesh_breadcrumbs(): void {
	if ( is_front_page() ) {
		return;
	}
	echo '<nav class="tripdesh-breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'tripdesh' ) . '">';
	echo '<a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'tripdesh' ) . '</a>';

	if ( is_singular() ) {
		$post_type = get_post_type();
		$archive   = get_post_type_archive_link( $post_type );
		if ( $archive ) {
			echo ' <span aria-hidden="true">/</span> <a href="' . esc_url( $archive ) . '">' . esc_html( get_post_type_object( $post_type )->labels->name ) . '</a>';
		}
		echo ' <span aria-hidden="true">/</span> <span aria-current="page">' . esc_html( get_the_title() ) . '</span>';
	} elseif ( is_post_type_archive() ) {
		echo ' <span aria-hidden="true">/</span> <span aria-current="page">' . esc_html( post_type_archive_title( '', false ) ) . '</span>';
	} elseif ( is_page() ) {
		echo ' <span aria-hidden="true">/</span> <span aria-current="page">' . esc_html( get_the_title() ) . '</span>';
	}

	echo '</nav>';
}

/**
 * Fallback menu when no "primary" menu is assigned yet, so the site
 * doesn't ship with an empty nav on first install.
 */
function tripdesh_fallback_menu(): void {
	$links = array(
		home_url( '/' )                      => __( 'Home', 'tripdesh' ),
		home_url( '/destinations/' )         => __( 'Destinations', 'tripdesh' ),
		home_url( '/tours/' )                => __( 'Tours', 'tripdesh' ),
		home_url( '/hotels/' )               => __( 'Hotels', 'tripdesh' ),
		home_url( '/blog/' )                 => __( 'Travel Guides', 'tripdesh' ),
		home_url( '/ai-trip-planner/' )      => __( 'AI Trip Planner', 'tripdesh' ),
		home_url( '/contact/' )              => __( 'Contact', 'tripdesh' ),
	);
	echo '<ul class="tripdesh-nav__menu">';
	foreach ( $links as $url => $label ) {
		echo '<li><a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a></li>';
	}
	echo '</ul>';
}
