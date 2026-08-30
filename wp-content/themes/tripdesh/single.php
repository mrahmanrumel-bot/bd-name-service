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
		<article <?php post_class( 'tripdesh-post' ); ?>>
			<header class="tripdesh-post__header">
				<h1><?php the_title(); ?></h1>
				<p class="tripdesh-post__meta"><?php echo esc_html( get_the_date() ); ?> &middot; <?php the_author(); ?></p>
			</header>
			<?php if ( has_post_thumbnail() ) : ?>
				<div class="tripdesh-post__thumb"><?php the_post_thumbnail( 'tripdesh-hero' ); ?></div>
			<?php endif; ?>
			<div class="tripdesh-post__content">
				<?php the_content(); ?>
			</div>
		</article>
		<?php
		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}
	endwhile;
	?>
</div>
<?php
get_footer();
