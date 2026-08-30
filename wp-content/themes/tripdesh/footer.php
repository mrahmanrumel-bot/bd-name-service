<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$settings = class_exists( 'Tripdesh_Core' ) ? Tripdesh_Core::instance()->settings : null;
$phone    = $settings ? $settings->get( 'contact_phone' ) : '';
$email    = $settings ? $settings->get( 'contact_email' ) : '';
$whatsapp = $settings ? $settings->get( 'contact_whatsapp' ) : '';
?>
</main>

<footer class="tripdesh-footer">
	<div class="tripdesh-container tripdesh-footer__grid">
		<div class="tripdesh-footer__col">
			<h4><?php bloginfo( 'name' ); ?></h4>
			<p><?php esc_html_e( 'Your trusted partner for exploring Bangladesh — Cox\'s Bazar, Sylhet, Bandarban, Sundarbans and beyond. Bengali & English support.', 'tripdesh' ); ?></p>
			<?php if ( $phone ) : ?><p><?php esc_html_e( 'Phone:', 'tripdesh' ); ?> <a href="tel:<?php echo esc_attr( $phone ); ?>"><?php echo esc_html( $phone ); ?></a></p><?php endif; ?>
			<?php if ( $email ) : ?><p><?php esc_html_e( 'Email:', 'tripdesh' ); ?> <a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></p><?php endif; ?>
			<?php if ( $whatsapp ) : ?><p><?php esc_html_e( 'WhatsApp:', 'tripdesh' ); ?> <a href="https://wa.me/<?php echo esc_attr( preg_replace( '/\D/', '', $whatsapp ) ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $whatsapp ); ?></a></p><?php endif; ?>
		</div>

		<div class="tripdesh-footer__col">
			<h4><?php esc_html_e( 'Explore', 'tripdesh' ); ?></h4>
			<ul>
				<li><a href="<?php echo esc_url( home_url( '/destinations/' ) ); ?>"><?php esc_html_e( 'Destinations', 'tripdesh' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/tours/' ) ); ?>"><?php esc_html_e( 'Tour Packages', 'tripdesh' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/hotels/' ) ); ?>"><?php esc_html_e( 'Hotels', 'tripdesh' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>"><?php esc_html_e( 'Travel Guides', 'tripdesh' ); ?></a></li>
			</ul>
		</div>

		<div class="tripdesh-footer__col">
			<h4><?php esc_html_e( 'Company', 'tripdesh' ); ?></h4>
			<ul>
				<li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><?php esc_html_e( 'About Us', 'tripdesh' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact', 'tripdesh' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/faq/' ) ); ?>"><?php esc_html_e( 'FAQ', 'tripdesh' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/terms-and-conditions/' ) ); ?>"><?php esc_html_e( 'Terms & Conditions', 'tripdesh' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>"><?php esc_html_e( 'Privacy Policy', 'tripdesh' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/cancellation-policy/' ) ); ?>"><?php esc_html_e( 'Cancellation Policy', 'tripdesh' ); ?></a></li>
			</ul>
		</div>

		<div class="tripdesh-footer__col">
			<h4><?php esc_html_e( 'Newsletter', 'tripdesh' ); ?></h4>
			<p><?php esc_html_e( 'Seasonal offers and travel guides, straight to your inbox.', 'tripdesh' ); ?></p>
			<?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
				<?php dynamic_sidebar( 'footer-1' ); ?>
			<?php else : ?>
				<p class="tripdesh-footer__hint"><?php esc_html_e( 'Add a newsletter signup widget under Appearance → Widgets → Footer Column 1.', 'tripdesh' ); ?></p>
			<?php endif; ?>
		</div>
	</div>

	<div class="tripdesh-container tripdesh-footer__bottom">
		<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'All rights reserved.', 'tripdesh' ); ?></p>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
