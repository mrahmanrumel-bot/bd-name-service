<?php
/**
 * Template Name: AI Trip Planner
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
?>
<div class="tripdesh-container tripdesh-section tripdesh-section--narrow">
	<h1><?php the_title(); ?></h1>
	<p><?php esc_html_e( "Tell the assistant your starting point, dates, budget, and who you're travelling with — it will suggest destinations and tour packages we actually offer, and build a day-by-day plan. Booking still goes through a real person before you pay.", 'tripdesh' ); ?></p>

	<?php echo do_shortcode( '[tripdesh_ai_chat]' ); ?>

	<div class="tripdesh-page__content"><?php the_content(); ?></div>
</div>
<?php
get_footer();
