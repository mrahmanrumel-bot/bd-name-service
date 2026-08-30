<?php
/**
 * Template Name: FAQ
 *
 * Write each question as an H3 heading immediately followed by a
 * paragraph answer in the block editor — the plugin's FAQPage schema
 * (Tripdesh_SEO_Schema::faq_schema) parses exactly that H3+P pattern out
 * of the content automatically.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
?>
<div class="tripdesh-container tripdesh-section tripdesh-section--narrow">
	<h1><?php the_title(); ?></h1>
	<div class="tripdesh-faq tripdesh-page__content"><?php the_content(); ?></div>
</div>
<?php
get_footer();
