<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();

while ( have_posts() ) :
	the_post();
	$id       = get_the_ID();
	$rating   = tripdesh_meta( $id, 'star_rating' );
	$price    = tripdesh_meta( $id, 'price_per_night' );
	$rooms    = tripdesh_meta( $id, 'room_types' );
	$amenities = tripdesh_meta( $id, 'amenities' );
	$policies = tripdesh_meta( $id, 'policies' );
	$address  = tripdesh_meta( $id, 'address' );
	$phone    = tripdesh_meta( $id, 'phone' );
	?>
	<div class="tripdesh-hero-page">
		<?php if ( has_post_thumbnail() ) : ?>
			<div class="tripdesh-hero-page__image"><?php the_post_thumbnail( 'tripdesh-hero' ); ?></div>
		<?php endif; ?>
		<div class="tripdesh-container tripdesh-hero-page__content">
			<h1><?php the_title(); ?></h1>
			<p class="tripdesh-hero-page__meta">
				<?php if ( $rating ) : ?><span aria-label="<?php echo esc_attr( $rating . ' star' ); ?>"><?php echo esc_html( str_repeat( '★', (int) $rating ) ); ?></span><?php endif; ?>
				<?php if ( $address ) : ?> &middot; <?php echo esc_html( $address ); ?><?php endif; ?>
			</p>
		</div>
	</div>

	<div class="tripdesh-container tripdesh-section tripdesh-layout">
		<div class="tripdesh-layout__main">
			<div class="tripdesh-post__content">
				<?php the_content(); ?>
			</div>

			<?php if ( $rooms ) : ?>
				<section class="tripdesh-info-block">
					<h2><?php esc_html_e( 'Room Types', 'tripdesh' ); ?></h2>
					<p><?php echo nl2br( esc_html( $rooms ) ); ?></p>
				</section>
			<?php endif; ?>

			<?php if ( $amenities ) : ?>
				<section class="tripdesh-info-block">
					<h2><?php esc_html_e( 'Amenities', 'tripdesh' ); ?></h2>
					<p><?php echo nl2br( esc_html( $amenities ) ); ?></p>
				</section>
			<?php endif; ?>

			<?php if ( $policies ) : ?>
				<section class="tripdesh-info-block">
					<h2><?php esc_html_e( 'Policies', 'tripdesh' ); ?></h2>
					<p><?php echo nl2br( esc_html( $policies ) ); ?></p>
				</section>
			<?php endif; ?>
		</div>

		<aside class="tripdesh-layout__sidebar">
			<div class="tripdesh-sidebar-card">
				<?php if ( $price ) : ?>
					<p class="tripdesh-sidebar-card__price"><?php echo esc_html( sprintf( __( '%s / night', 'tripdesh' ), tripdesh_price( $price ) ) ); ?></p>
				<?php endif; ?>
				<?php if ( $phone ) : ?>
					<p><?php esc_html_e( 'Contact:', 'tripdesh' ); ?> <a href="tel:<?php echo esc_attr( $phone ); ?>"><?php echo esc_html( $phone ); ?></a></p>
				<?php endif; ?>
				<?php echo do_shortcode( '[tripdesh_booking_form id="' . $id . '"]' ); ?>
			</div>
		</aside>
	</div>
	<?php
endwhile;

get_footer();
