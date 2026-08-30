<?php
/**
 * Template Name: Deals
 *
 * Lists every published tour package that has a "Deal / Sale Price" set
 * in its Tripdesh Details meta box (must be lower than the regular
 * price). No separate coupon/discount system exists yet (Phase 2, see
 * ARCHITECTURE.md) — this is the simple, honest v1: mark a tour on sale
 * by filling in its sale price, and it appears here and on its card
 * automatically.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();

$deals = get_posts(
	array(
		'post_type'      => 'tour_package',
		'posts_per_page' => 24,
		'meta_query'      => array(
			array(
				'key'     => '_tripdesh_sale_price',
				'value'   => '',
				'compare' => '!=',
			),
		),
	)
);
$deals = array_values( array_filter( $deals, fn( $post ) => tripdesh_is_on_sale( $post->ID ) ) );
?>
<div class="tripdesh-container tripdesh-section">
	<h1><?php the_title(); ?></h1>
	<div class="tripdesh-page__content"><?php the_content(); ?></div>

	<?php if ( $deals ) : ?>
		<div class="tripdesh-card-grid">
			<?php foreach ( $deals as $d ) : tripdesh_render_card( $d ); endforeach; ?>
		</div>
	<?php else : ?>
		<p><?php esc_html_e( 'No active deals right now — check back soon.', 'tripdesh' ); ?></p>
	<?php endif; ?>
</div>
<?php
get_footer();
