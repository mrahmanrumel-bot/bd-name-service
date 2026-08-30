<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Meta tags + JSON-LD structured data (brief §17). Only runs when no SEO
 * plugin is already active, to avoid duplicate/conflicting output.
 */
class Tripdesh_SEO_Schema {

	public function __construct() {
		add_action( 'wp_head', array( $this, 'output_meta_tags' ), 1 );
		add_action( 'wp_head', array( $this, 'output_json_ld' ), 5 );
	}

	private function seo_plugin_active(): bool {
		return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'SEOPRESS_VERSION' );
	}

	public function output_meta_tags(): void {
		if ( $this->seo_plugin_active() || ! is_singular() ) {
			return;
		}

		global $post;
		$title       = wp_strip_all_tags( get_the_title( $post ) ) . ' — ' . get_bloginfo( 'name' );
		$description = $this->get_description( $post );
		$url         = get_permalink( $post );
		$image       = get_the_post_thumbnail_url( $post, 'large' );

		echo "\n<!-- Tripdesh SEO -->\n";
		printf( '<meta name="description" content="%s" />' . "\n", esc_attr( $description ) );
		printf( '<link rel="canonical" href="%s" />' . "\n", esc_url( $url ) );
		printf( '<meta property="og:title" content="%s" />' . "\n", esc_attr( $title ) );
		printf( '<meta property="og:description" content="%s" />' . "\n", esc_attr( $description ) );
		printf( '<meta property="og:url" content="%s" />' . "\n", esc_url( $url ) );
		printf( '<meta property="og:type" content="website" />' . "\n" );
		if ( $image ) {
			printf( '<meta property="og:image" content="%s" />' . "\n", esc_url( $image ) );
		}
		printf( '<meta name="twitter:card" content="%s" />' . "\n", $image ? 'summary_large_image' : 'summary' );
		printf( '<meta name="twitter:title" content="%s" />' . "\n", esc_attr( $title ) );
		printf( '<meta name="twitter:description" content="%s" />' . "\n", esc_attr( $description ) );
	}

	private function get_description( WP_Post $post ): string {
		$excerpt = has_excerpt( $post ) ? get_the_excerpt( $post ) : wp_trim_words( wp_strip_all_tags( $post->post_content ), 30 );
		return wp_strip_all_tags( $excerpt );
	}

	public function output_json_ld(): void {
		if ( $this->seo_plugin_active() || ! is_singular() ) {
			return;
		}

		$schema = null;

		if ( is_singular( 'destination' ) ) {
			$schema = $this->destination_schema( get_queried_object() );
		} elseif ( is_singular( 'tour_package' ) ) {
			$schema = $this->tour_schema( get_queried_object() );
		} elseif ( is_singular( 'hotel' ) ) {
			$schema = $this->hotel_schema( get_queried_object() );
		} elseif ( is_page_template( 'page-templates/template-faq.php' ) ) {
			$schema = $this->faq_schema();
		}

		if ( $schema ) {
			echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
		}
	}

	private function destination_schema( WP_Post $post ): array {
		return array(
			'@context'    => 'https://schema.org',
			'@type'       => 'TouristDestination',
			'name'        => get_the_title( $post ),
			'description' => $this->get_description( $post ),
			'url'         => get_permalink( $post ),
			'image'       => get_the_post_thumbnail_url( $post, 'large' ) ?: null,
		);
	}

	private function tour_schema( WP_Post $post ): array {
		$price = get_post_meta( $post->ID, '_tripdesh_price', true );
		$days  = get_post_meta( $post->ID, '_tripdesh_duration_days', true );

		$schema = array(
			'@context'    => 'https://schema.org',
			'@type'       => 'TouristTrip',
			'name'        => get_the_title( $post ),
			'description' => $this->get_description( $post ),
			'url'         => get_permalink( $post ),
			'image'       => get_the_post_thumbnail_url( $post, 'large' ) ?: null,
		);

		if ( $days ) {
			$schema['itinerary'] = sprintf( '%d days', (int) $days );
		}
		if ( $price ) {
			$schema['offers'] = array(
				'@type'         => 'Offer',
				'price'         => (string) $price,
				'priceCurrency' => Tripdesh_Core::instance()->settings->get( 'currency', 'BDT' ),
				'url'           => get_permalink( $post ),
			);
		}

		return $schema;
	}

	private function hotel_schema( WP_Post $post ): array {
		$rating = get_post_meta( $post->ID, '_tripdesh_star_rating', true );
		$price  = get_post_meta( $post->ID, '_tripdesh_price_per_night', true );

		$schema = array(
			'@context'    => 'https://schema.org',
			'@type'       => 'Hotel',
			'name'        => get_the_title( $post ),
			'description' => $this->get_description( $post ),
			'url'         => get_permalink( $post ),
			'image'       => get_the_post_thumbnail_url( $post, 'large' ) ?: null,
		);

		if ( $rating ) {
			$schema['starRating'] = array(
				'@type'    => 'Rating',
				'ratingValue' => (string) $rating,
			);
		}
		if ( $price ) {
			$schema['priceRange'] = $price . '+ ' . Tripdesh_Core::instance()->settings->get( 'currency', 'BDT' );
		}

		return $schema;
	}

	private function faq_schema(): ?array {
		global $post;
		if ( ! preg_match_all( '/<h3[^>]*>(.*?)<\/h3>\s*<p[^>]*>(.*?)<\/p>/is', $post->post_content, $matches, PREG_SET_ORDER ) ) {
			return null;
		}

		$items = array();
		foreach ( $matches as $match ) {
			$items[] = array(
				'@type'          => 'Question',
				'name'           => wp_strip_all_tags( $match[1] ),
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => wp_strip_all_tags( $match[2] ),
				),
			);
		}

		if ( ! $items ) {
			return null;
		}

		return array(
			'@context'   => 'https://schema.org',
			'@type'      => 'FAQPage',
			'mainEntity' => $items,
		);
	}
}
