<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shared template helpers used across archive/single templates.
 */

function tripdesh_currency(): string {
	return class_exists( 'Tripdesh_Core' ) ? Tripdesh_Core::instance()->settings->get( 'currency', 'BDT' ) : 'BDT';
}

function tripdesh_price( $amount ): string {
	if ( '' === $amount || null === $amount ) {
		return '';
	}
	return number_format_i18n( (float) $amount ) . ' ' . tripdesh_currency();
}

function tripdesh_meta( int $post_id, string $key ) {
	return get_post_meta( $post_id, '_tripdesh_' . $key, true );
}

/**
 * Renders a destination/tour/hotel summary card. Kept generic so archive
 * loops for all three post types can share one partial.
 */
function tripdesh_render_card( WP_Post $post ): void {
	$type = $post->post_type;
	$meta_line = '';

	$on_sale = false;

	if ( 'tour_package' === $type ) {
		$days       = tripdesh_meta( $post->ID, 'duration_days' );
		$price      = tripdesh_meta( $post->ID, 'price' );
		$sale_price = tripdesh_meta( $post->ID, 'sale_price' );
		$on_sale    = $sale_price && $price && (float) $sale_price < (float) $price;
		$parts      = array();
		if ( $days ) {
			/* translators: %s: number of days */
			$parts[] = sprintf( _n( '%s day', '%s days', (int) $days, 'tripdesh' ), $days );
		}
		if ( $on_sale ) {
			$parts[] = '<s>' . tripdesh_price( $price ) . '</s> ' . tripdesh_price( $sale_price );
		} elseif ( $price ) {
			$parts[] = tripdesh_price( $price );
		}
		$meta_line = implode( ' &middot; ', $parts );
	} elseif ( 'hotel' === $type ) {
		$rating = tripdesh_meta( $post->ID, 'star_rating' );
		$price  = tripdesh_meta( $post->ID, 'price_per_night' );
		$parts  = array();
		if ( $rating ) {
			$parts[] = str_repeat( '★', (int) $rating );
		}
		if ( $price ) {
			/* translators: %s: price */
			$parts[] = sprintf( __( '%s / night', 'tripdesh' ), tripdesh_price( $price ) );
		}
		$meta_line = implode( ' &middot; ', $parts );
	} elseif ( 'destination' === $type ) {
		$budget = tripdesh_meta( $post->ID, 'estimated_budget' );
		if ( $budget ) {
			/* translators: %s: budget */
			$meta_line = sprintf( __( 'From %s / person', 'tripdesh' ), tripdesh_price( $budget ) );
		}
	}
	?>
	<a class="tripdesh-card" href="<?php echo esc_url( get_permalink( $post ) ); ?>">
		<div class="tripdesh-card__image">
			<?php if ( has_post_thumbnail( $post ) ) : ?>
				<?php echo get_the_post_thumbnail( $post, 'tripdesh-card' ); ?>
			<?php else : ?>
				<div class="tripdesh-card__placeholder" aria-hidden="true"></div>
			<?php endif; ?>
		</div>
		<div class="tripdesh-card__body">
			<?php if ( $on_sale ) : ?>
				<span class="tripdesh-badge tripdesh-badge--sale"><?php esc_html_e( 'Deal', 'tripdesh' ); ?></span>
			<?php endif; ?>
			<h3 class="tripdesh-card__title"><?php echo esc_html( get_the_title( $post ) ); ?></h3>
			<?php if ( $meta_line ) : ?>
				<p class="tripdesh-card__meta"><?php echo wp_kses_post( $meta_line ); ?></p>
			<?php endif; ?>
		</div>
	</a>
	<?php
}

/**
 * True when a tour package has a valid, lower sale_price set.
 */
function tripdesh_is_on_sale( int $post_id ): bool {
	$price      = tripdesh_meta( $post_id, 'price' );
	$sale_price = tripdesh_meta( $post_id, 'sale_price' );
	return (bool) ( $sale_price && $price && (float) $sale_price < (float) $price );
}

function tripdesh_render_itinerary( array $days ): void {
	if ( ! $days ) {
		return;
	}
	echo '<ol class="tripdesh-itinerary">';
	foreach ( $days as $day ) {
		echo '<li class="tripdesh-itinerary__day">';
		/* translators: %d: day number */
		echo '<span class="tripdesh-itinerary__label">' . esc_html( sprintf( __( 'Day %d', 'tripdesh' ), $day['day'] ) ) . '</span>';
		echo '<span class="tripdesh-itinerary__desc">' . esc_html( $day['description'] ) . '</span>';
		echo '</li>';
	}
	echo '</ol>';
}
