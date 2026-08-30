<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();

$destinations = get_posts( array( 'post_type' => 'destination', 'posts_per_page' => 8 ) );
$tours        = get_posts( array( 'post_type' => 'tour_package', 'posts_per_page' => 6 ) );
$hotels       = get_posts( array( 'post_type' => 'hotel', 'posts_per_page' => 4 ) );
$testimonials = get_posts( array( 'post_type' => 'testimonial', 'posts_per_page' => 6 ) );
$guides       = get_posts( array( 'post_type' => 'post', 'posts_per_page' => 3 ) );
$tea_garden   = get_posts(
	array(
		'post_type'      => 'destination',
		'posts_per_page' => 6,
		'meta_key'       => '_tripdesh_featured_collection',
		'meta_value'     => 'tea_garden',
	)
);

$family_tours    = get_posts( array( 'post_type' => 'tour_package', 'posts_per_page' => 3, 'tax_query' => array( array( 'taxonomy' => 'travel_style', 'field' => 'slug', 'terms' => 'family' ) ) ) );
$couple_tours    = get_posts( array( 'post_type' => 'tour_package', 'posts_per_page' => 3, 'tax_query' => array( array( 'taxonomy' => 'travel_style', 'field' => 'slug', 'terms' => 'couple' ) ) ) );
$adventure_tours = get_posts( array( 'post_type' => 'tour_package', 'posts_per_page' => 3, 'tax_query' => array( array( 'taxonomy' => 'travel_style', 'field' => 'slug', 'terms' => 'adventure' ) ) ) );
$weekend_tours   = get_posts( array( 'post_type' => 'tour_package', 'posts_per_page' => 3, 'tax_query' => array( array( 'taxonomy' => 'travel_style', 'field' => 'slug', 'terms' => 'weekend' ) ) ) );
?>

<section class="tripdesh-hero">
	<div class="tripdesh-container tripdesh-hero__inner">
		<h1 class="tripdesh-hero__title"><?php esc_html_e( 'Travel Bangladesh, with Tripdesh', 'tripdesh' ); ?></h1>
		<p class="tripdesh-hero__subtitle"><?php esc_html_e( 'Find the best travel experiences in Bangladesh, matched to your budget, time, and taste.', 'tripdesh' ); ?></p>
		<h2 class="tripdesh-hero__search-heading"><?php esc_html_e( 'Where do you want to go?', 'tripdesh' ); ?></h2>
		<?php echo do_shortcode( '[tripdesh_search]' ); ?>
	</div>
</section>

<?php if ( $destinations ) : ?>
<section class="tripdesh-section tripdesh-container">
	<div class="tripdesh-section__header">
		<h2><?php esc_html_e( 'Popular Destinations', 'tripdesh' ); ?></h2>
		<a class="tripdesh-link" href="<?php echo esc_url( home_url( '/destinations/' ) ); ?>"><?php esc_html_e( 'View all', 'tripdesh' ); ?> &rarr;</a>
	</div>
	<div class="tripdesh-card-grid">
		<?php foreach ( $destinations as $d ) : tripdesh_render_card( $d ); endforeach; ?>
	</div>
</section>
<?php endif; ?>

<?php if ( $tea_garden ) : ?>
<section class="tripdesh-section tripdesh-section--tea">
	<div class="tripdesh-container">
		<div class="tripdesh-section__header">
			<div>
				<span class="tripdesh-badge"><?php esc_html_e( "Sylhet's Tea Gardens", 'tripdesh' ); ?></span>
				<h2><?php esc_html_e( 'Travel to the Land of Tea', 'tripdesh' ); ?></h2>
			</div>
		</div>
		<div class="tripdesh-card-grid">
			<?php foreach ( $tea_garden as $d ) : tripdesh_render_card( $d ); endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php if ( $tours ) : ?>
<section class="tripdesh-section tripdesh-section--alt">
	<div class="tripdesh-container">
		<div class="tripdesh-section__header">
			<h2><?php esc_html_e( 'Popular Tour Packages', 'tripdesh' ); ?></h2>
			<a class="tripdesh-link" href="<?php echo esc_url( home_url( '/tours/' ) ); ?>"><?php esc_html_e( 'View all', 'tripdesh' ); ?> &rarr;</a>
		</div>
		<div class="tripdesh-card-grid">
			<?php foreach ( $tours as $t ) : tripdesh_render_card( $t ); endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php if ( $hotels ) : ?>
<section class="tripdesh-section tripdesh-container">
	<div class="tripdesh-section__header">
		<h2><?php esc_html_e( 'Recommended Hotels', 'tripdesh' ); ?></h2>
		<a class="tripdesh-link" href="<?php echo esc_url( home_url( '/hotels/' ) ); ?>"><?php esc_html_e( 'View all', 'tripdesh' ); ?> &rarr;</a>
	</div>
	<div class="tripdesh-card-grid">
		<?php foreach ( $hotels as $h ) : tripdesh_render_card( $h ); endforeach; ?>
	</div>
</section>
<?php endif; ?>

<?php if ( $weekend_tours ) : ?>
<section class="tripdesh-section tripdesh-container">
	<div class="tripdesh-section__header"><h2><?php esc_html_e( 'Weekend Trips', 'tripdesh' ); ?></h2></div>
	<div class="tripdesh-card-grid tripdesh-card-grid--3"><?php foreach ( $weekend_tours as $t ) : tripdesh_render_card( $t ); endforeach; ?></div>
</section>
<?php endif; ?>

<?php if ( $family_tours ) : ?>
<section class="tripdesh-section tripdesh-container">
	<div class="tripdesh-section__header"><h2><?php esc_html_e( 'Family Tours', 'tripdesh' ); ?></h2></div>
	<div class="tripdesh-card-grid tripdesh-card-grid--3"><?php foreach ( $family_tours as $t ) : tripdesh_render_card( $t ); endforeach; ?></div>
</section>
<?php endif; ?>

<?php if ( $couple_tours ) : ?>
<section class="tripdesh-section tripdesh-container">
	<div class="tripdesh-section__header"><h2><?php esc_html_e( 'Couple Packages', 'tripdesh' ); ?></h2></div>
	<div class="tripdesh-card-grid tripdesh-card-grid--3"><?php foreach ( $couple_tours as $t ) : tripdesh_render_card( $t ); endforeach; ?></div>
</section>
<?php endif; ?>

<?php if ( $adventure_tours ) : ?>
<section class="tripdesh-section tripdesh-container">
	<div class="tripdesh-section__header"><h2><?php esc_html_e( 'Adventure Tours', 'tripdesh' ); ?></h2></div>
	<div class="tripdesh-card-grid tripdesh-card-grid--3"><?php foreach ( $adventure_tours as $t ) : tripdesh_render_card( $t ); endforeach; ?></div>
</section>
<?php endif; ?>

<section class="tripdesh-section tripdesh-section--alt">
	<div class="tripdesh-container tripdesh-why">
		<h2><?php esc_html_e( 'Why Choose Tripdesh', 'tripdesh' ); ?></h2>
		<div class="tripdesh-why__grid">
			<div class="tripdesh-why__item">
				<h3><?php esc_html_e( 'Local Experts', 'tripdesh' ); ?></h3>
				<p><?php esc_html_e( 'Real knowledge of Bangladesh destinations, not generic packages.', 'tripdesh' ); ?></p>
			</div>
			<div class="tripdesh-why__item">
				<h3><?php esc_html_e( 'AI Trip Planning', 'tripdesh' ); ?></h3>
				<p><?php esc_html_e( 'Tell us your budget and dates — get a plan in seconds, in Bangla or English.', 'tripdesh' ); ?></p>
			</div>
			<div class="tripdesh-why__item">
				<h3><?php esc_html_e( 'Transparent Pricing', 'tripdesh' ); ?></h3>
				<p><?php esc_html_e( 'Clear BDT pricing, no hidden fees.', 'tripdesh' ); ?></p>
			</div>
			<div class="tripdesh-why__item">
				<h3><?php esc_html_e( 'Human Support', 'tripdesh' ); ?></h3>
				<p><?php esc_html_e( 'A real team confirms every booking before you pay.', 'tripdesh' ); ?></p>
			</div>
		</div>
	</div>
</section>

<?php if ( $testimonials ) : ?>
<section class="tripdesh-section tripdesh-container">
	<div class="tripdesh-section__header"><h2><?php esc_html_e( 'What Travelers Say', 'tripdesh' ); ?></h2></div>
	<div class="tripdesh-testimonial-grid">
		<?php foreach ( $testimonials as $t ) : ?>
			<blockquote class="tripdesh-testimonial">
				<p>&ldquo;<?php echo esc_html( wp_strip_all_tags( $t->post_content ) ); ?>&rdquo;</p>
				<footer>
					<?php echo esc_html( tripdesh_meta( $t->ID, 'customer_name' ) ?: get_the_title( $t ) ); ?>
					<?php $rating = tripdesh_meta( $t->ID, 'rating' ); ?>
					<?php if ( $rating ) : ?><span class="tripdesh-testimonial__rating"><?php echo esc_html( str_repeat( '★', (int) $rating ) ); ?></span><?php endif; ?>
				</footer>
			</blockquote>
		<?php endforeach; ?>
	</div>
</section>
<?php endif; ?>

<?php if ( $guides ) : ?>
<section class="tripdesh-section tripdesh-section--alt">
	<div class="tripdesh-container">
		<div class="tripdesh-section__header">
			<h2><?php esc_html_e( 'Travel Guides', 'tripdesh' ); ?></h2>
			<a class="tripdesh-link" href="<?php echo esc_url( home_url( '/blog/' ) ); ?>"><?php esc_html_e( 'View all', 'tripdesh' ); ?> &rarr;</a>
		</div>
		<div class="tripdesh-blog-grid">
			<?php foreach ( $guides as $post ) : setup_postdata( $post ); get_template_part( 'template-parts/content', 'excerpt' ); endforeach; wp_reset_postdata(); ?>
		</div>
	</div>
</section>
<?php endif; ?>

<section class="tripdesh-section tripdesh-container">
	<div class="tripdesh-ai-promo">
		<div>
			<h2><?php esc_html_e( 'Not sure where to go?', 'tripdesh' ); ?></h2>
			<p><?php esc_html_e( 'Chat with our AI Travel Assistant in Bangla or English and get a personalized plan.', 'tripdesh' ); ?></p>
		</div>
		<?php echo do_shortcode( '[tripdesh_ai_chat]' ); ?>
	</div>
</section>

<?php
get_footer();
