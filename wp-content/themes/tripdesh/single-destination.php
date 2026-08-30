<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();

while ( have_posts() ) :
	the_post();
	$id = get_the_ID();

	$fields = array(
		'best_time_to_visit' => __( 'Best Time to Visit', 'tripdesh' ),
		'how_to_reach'        => __( 'How to Reach', 'tripdesh' ),
		'where_to_stay'       => __( 'Where to Stay', 'tripdesh' ),
		'things_to_do'        => __( 'Things to Do', 'tripdesh' ),
		'food'                => __( 'Food', 'tripdesh' ),
		'safety_info'         => __( 'Safety Information', 'tripdesh' ),
		'local_transport'     => __( 'Local Transportation', 'tripdesh' ),
	);
	?>
	<div class="tripdesh-hero-page">
		<?php if ( has_post_thumbnail() ) : ?>
			<div class="tripdesh-hero-page__image"><?php the_post_thumbnail( 'tripdesh-hero' ); ?></div>
		<?php endif; ?>
		<div class="tripdesh-container tripdesh-hero-page__content">
			<h1><?php the_title(); ?></h1>
			<?php $budget = tripdesh_meta( $id, 'estimated_budget' ); ?>
			<?php if ( $budget ) : ?>
				<p class="tripdesh-hero-page__budget"><?php echo esc_html( sprintf( __( 'Estimated budget: %s / person', 'tripdesh' ), tripdesh_price( $budget ) ) ); ?></p>
			<?php endif; ?>
		</div>
	</div>

	<div class="tripdesh-container tripdesh-section tripdesh-layout">
		<div class="tripdesh-layout__main">
			<div class="tripdesh-post__content">
				<?php the_content(); ?>
			</div>

			<?php foreach ( $fields as $key => $label ) : ?>
				<?php $value = tripdesh_meta( $id, $key ); ?>
				<?php if ( $value ) : ?>
					<section class="tripdesh-info-block">
						<h2><?php echo esc_html( $label ); ?></h2>
						<p><?php echo nl2br( esc_html( $value ) ); ?></p>
					</section>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>

		<aside class="tripdesh-layout__sidebar">
			<div class="tripdesh-sidebar-card">
				<h3><?php esc_html_e( 'Tours to this Destination', 'tripdesh' ); ?></h3>
				<?php
				$tours = get_posts(
					array(
						'post_type'      => 'tour_package',
						'posts_per_page' => 5,
						'tax_query'      => array(
							array(
								'taxonomy' => 'tripdesh_location',
								'field'    => 'slug',
								'terms'    => get_post_field( 'post_name', $id ),
							),
						),
					)
				);
				if ( $tours ) :
					?>
					<ul class="tripdesh-sidebar-list">
						<?php foreach ( $tours as $tour ) : ?>
							<li><a href="<?php echo esc_url( get_permalink( $tour ) ); ?>"><?php echo esc_html( get_the_title( $tour ) ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				<?php else : ?>
					<p><?php esc_html_e( 'No tour packages listed yet for this destination.', 'tripdesh' ); ?></p>
				<?php endif; ?>
				<a class="tripdesh-button tripdesh-button--block" href="<?php echo esc_url( home_url( '/ai-trip-planner/' ) ); ?>"><?php esc_html_e( 'Ask the AI to Plan a Trip Here', 'tripdesh' ); ?></a>
			</div>
		</aside>
	</div>
	<?php
endwhile;

get_footer();
