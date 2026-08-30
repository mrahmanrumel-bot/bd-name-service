<?php
/**
 * Template Name: About
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
?>
<div class="tripdesh-container tripdesh-section tripdesh-section--narrow">
	<h1><?php the_title(); ?></h1>
	<div class="tripdesh-page__content"><?php the_content(); ?></div>
</div>
<?php
get_footer();
