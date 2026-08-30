<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article <?php post_class( 'tripdesh-blog-card' ); ?>>
	<a href="<?php the_permalink(); ?>" class="tripdesh-blog-card__image">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( 'tripdesh-card' ); ?>
		<?php else : ?>
			<div class="tripdesh-card__placeholder" aria-hidden="true"></div>
		<?php endif; ?>
	</a>
	<div class="tripdesh-blog-card__body">
		<h2 class="tripdesh-blog-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<p class="tripdesh-blog-card__meta"><?php echo esc_html( get_the_date() ); ?></p>
		<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24 ) ); ?></p>
		<a class="tripdesh-link" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read more', 'tripdesh' ); ?> &rarr;</a>
	</div>
</article>
