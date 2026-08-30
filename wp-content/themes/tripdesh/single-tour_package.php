<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();

while ( have_posts() ) :
	the_post();
	$id        = get_the_ID();
	$days      = tripdesh_meta( $id, 'duration_days' );
	$nights    = tripdesh_meta( $id, 'duration_nights' );
	$price     = tripdesh_meta( $id, 'price' );
	$inclusions = tripdesh_meta( $id, 'inclusions' );
	$exclusions = tripdesh_meta( $id, 'exclusions' );
	$itinerary  = Tripdesh_Meta_Boxes::parse_itinerary( (string) tripdesh_meta( $id, 'itinerary' ) );
	$terms      = tripdesh_meta( $id, 'terms' );
	$departure  = tripdesh_meta( $id, 'departure_location' );
	$tiers      = get_the_terms( $id, 'tour_type' );
	?>
	<div class="tripdesh-hero-page">
		<?php if ( has_post_thumbnail() ) : ?>
			<div class="tripdesh-hero-page__image"><?php the_post_thumbnail( 'tripdesh-hero' ); ?></div>
		<?php endif; ?>
		<div class="tripdesh-container tripdesh-hero-page__content">
			<?php if ( $tiers && ! is_wp_error( $tiers ) ) : ?>
				<span class="tripdesh-badge"><?php echo esc_html( $tiers[0]->name ); ?></span>
			<?php endif; ?>
			<h1><?php the_title(); ?></h1>
			<p class="tripdesh-hero-page__meta">
				<?php if ( $days ) : ?>
					<?php echo esc_html( sprintf( __( '%1$s days / %2$s nights', 'tripdesh' ), $days, $nights ?: max( 0, (int) $days - 1 ) ) ); ?>
				<?php endif; ?>
				<?php if ( $departure ) : ?>
					&middot; <?php echo esc_html( sprintf( __( 'Departs from %s', 'tripdesh' ), $departure ) ); ?>
				<?php endif; ?>
			</p>
		</div>
	</div>

	<div class="tripdesh-container tripdesh-section tripdesh-layout">
		<div class="tripdesh-layout__main">
			<div class="tripdesh-post__content">
				<?php the_content(); ?>
			</div>

			<?php if ( $itinerary ) : ?>
				<section class="tripdesh-info-block">
					<h2><?php esc_html_e( 'Day-by-Day Itinerary', 'tripdesh' ); ?></h2>
					<?php tripdesh_render_itinerary( $itinerary ); ?>
				</section>
			<?php endif; ?>

			<?php if ( $inclusions ) : ?>
				<section class="tripdesh-info-block">
					<h2><?php esc_html_e( 'What\'s Included', 'tripdesh' ); ?></h2>
					<p><?php echo nl2br( esc_html( $inclusions ) ); ?></p>
				</section>
			<?php endif; ?>

			<?php if ( $exclusions ) : ?>
				<section class="tripdesh-info-block">
					<h2><?php esc_html_e( 'Not Included', 'tripdesh' ); ?></h2>
					<p><?php echo nl2br( esc_html( $exclusions ) ); ?></p>
				</section>
			<?php endif; ?>

			<?php if ( $terms ) : ?>
				<section class="tripdesh-info-block">
					<h2><?php esc_html_e( 'Terms & Conditions', 'tripdesh' ); ?></h2>
					<p><?php echo nl2br( esc_html( $terms ) ); ?></p>
				</section>
			<?php endif; ?>
		</div>

		<aside class="tripdesh-layout__sidebar">
			<div class="tripdesh-sidebar-card">
				<?php echo do_shortcode( '[tripdesh_booking_form id="' . $id . '"]' ); ?>
			</div>
		</aside>
	</div>
	<?php
endwhile;

get_footer();
