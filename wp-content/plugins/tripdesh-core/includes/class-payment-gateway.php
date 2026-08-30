<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Payment gateway seam for Phase 2 (see ARCHITECTURE.md §8). No live
 * gateway is wired up in this pass — there are no merchant credentials to
 * integrate against yet, and a fake integration would be worse than none.
 *
 * When SSLCommerz (recommended: one integration covers bKash/Nagad/
 * Rocket/cards/bank transfer for BDT) or a direct bKash Merchant API
 * integration is ready, implement create_payment_session() to call the
 * gateway's session API and return the redirect URL; Tripdesh_Booking
 * already calls this class at the right point in the booking flow.
 */
class Tripdesh_Payment_Gateway {

	/**
	 * Starts a payment session for a booking and returns a redirect URL,
	 * or null if no gateway is configured (manual-confirmation mode).
	 */
	public function create_payment_session( int $booking_id ): ?string {
		$settings = Tripdesh_Core::instance()->settings;
		$gateway  = $settings->get( 'payment_gateway', 'none' );

		if ( 'none' === $gateway ) {
			return null;
		}

		/**
		 * Fires when a real gateway is selected but not yet implemented,
		 * so it's visible in logs/monitoring rather than silently no-op'ing.
		 */
		do_action( 'tripdesh_payment_gateway_not_implemented', $gateway, $booking_id );

		return null;
	}
}
