<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
?>
<div class="tripdesh-container tripdesh-section tripdesh-section--narrow">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<article <?php post_class( 'tripdesh-page' ); ?>>
			<h1><?php the_title(); ?></h1>
			<div class="tripdesh-page__content">
				<?php the_content(); ?>
			</div>
		</article>
		<?php
	endwhile;
	?>
</div>
<?php
get_footer();
