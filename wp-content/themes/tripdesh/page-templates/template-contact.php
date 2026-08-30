<?php
/**
 * Template Name: Contact
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();

$settings = class_exists( 'Tripdesh_Core' ) ? Tripdesh_Core::instance()->settings : null;
$phone    = $settings ? $settings->get( 'contact_phone' ) : '';
$email    = $settings ? $settings->get( 'contact_email' ) : '';
$whatsapp = $settings ? $settings->get( 'contact_whatsapp' ) : '';
?>
<div class="tripdesh-container tripdesh-section tripdesh-section--narrow">
	<h1><?php the_title(); ?></h1>
	<div class="tripdesh-page__content"><?php the_content(); ?></div>

	<div class="tripdesh-contact-grid">
		<?php if ( $phone ) : ?>
			<div class="tripdesh-contact-item">
				<h3><?php esc_html_e( 'Phone', 'tripdesh' ); ?></h3>
				<p><a href="tel:<?php echo esc_attr( $phone ); ?>"><?php echo esc_html( $phone ); ?></a></p>
			</div>
		<?php endif; ?>
		<?php if ( $email ) : ?>
			<div class="tripdesh-contact-item">
				<h3><?php esc_html_e( 'Email', 'tripdesh' ); ?></h3>
				<p><a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></p>
			</div>
		<?php endif; ?>
		<?php if ( $whatsapp ) : ?>
			<div class="tripdesh-contact-item">
				<h3><?php esc_html_e( 'WhatsApp', 'tripdesh' ); ?></h3>
				<p><a href="https://wa.me/<?php echo esc_attr( preg_replace( '/\D/', '', $whatsapp ) ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $whatsapp ); ?></a></p>
			</div>
		<?php endif; ?>
	</div>

	<p class="tripdesh-contact-hint"><?php esc_html_e( 'Add a contact form via a plugin (e.g. Contact Form 7) below this content block, or configure phone/email/WhatsApp under Settings → Tripdesh.', 'tripdesh' ); ?></p>
</div>
<?php
get_footer();
