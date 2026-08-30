<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
?>
<div class="tripdesh-container tripdesh-section">
	<?php if ( have_posts() ) : ?>
		<div class="tripdesh-blog-grid">
			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/content', 'excerpt' );
			endwhile;
			?>
		</div>
		<div class="tripdesh-pagination">
			<?php the_posts_pagination(); ?>
		</div>
	<?php else : ?>
		<p><?php esc_html_e( 'Nothing found.', 'tripdesh' ); ?></p>
	<?php endif; ?>
</div>
<?php
get_footer();
