<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
?>
<div class="tripdesh-container tripdesh-section">
	<h1 class="tripdesh-archive__title"><?php the_archive_title(); ?></h1>
	<?php the_archive_description( '<div class="tripdesh-archive__description">', '</div>' ); ?>

	<?php if ( have_posts() ) : ?>
		<div class="tripdesh-card-grid">
			<?php
			while ( have_posts() ) :
				the_post();
				global $post;
				tripdesh_render_card( $post );
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
