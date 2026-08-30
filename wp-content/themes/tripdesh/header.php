<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="tripdesh-skip-link screen-reader-text" href="#tripdesh-content"><?php esc_html_e( 'Skip to content', 'tripdesh' ); ?></a>

<header class="tripdesh-header">
	<div class="tripdesh-container tripdesh-header__inner">
		<a class="tripdesh-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php bloginfo( 'name' ); ?>
		</a>

		<button class="tripdesh-nav-toggle" id="tripdesh-nav-toggle" aria-expanded="false" aria-controls="tripdesh-nav">
			<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'tripdesh' ); ?></span>
			<span class="tripdesh-nav-toggle__bar"></span>
			<span class="tripdesh-nav-toggle__bar"></span>
			<span class="tripdesh-nav-toggle__bar"></span>
		</button>

		<nav class="tripdesh-nav" id="tripdesh-nav" aria-label="<?php esc_attr_e( 'Primary', 'tripdesh' ); ?>">
			<?php
			if ( has_nav_menu( 'primary' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 'tripdesh-nav__menu',
						'fallback_cb'    => false,
					)
				);
			} else {
				tripdesh_fallback_menu();
			}
			?>
			<a class="tripdesh-nav__cta" href="<?php echo esc_url( home_url( '/ai-trip-planner/' ) ); ?>"><?php esc_html_e( 'Plan My Trip', 'tripdesh' ); ?></a>
		</nav>
	</div>
</header>

<?php if ( ! is_front_page() ) : ?>
	<div class="tripdesh-container">
		<?php tripdesh_breadcrumbs(); ?>
	</div>
<?php endif; ?>

<main id="tripdesh-content" class="tripdesh-main">
