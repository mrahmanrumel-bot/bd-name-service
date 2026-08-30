<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Public REST endpoints: booking capture and the AI concierge proxy.
 * Both are rate-limited per IP via a transient counter — a lightweight
 * abuse guard, not a substitute for a real WAF in production.
 */
class Tripdesh_REST_Api {

	const NAMESPACE = 'tripdesh/v1';

	private Tripdesh_Booking $booking;
	private Tripdesh_AI_Concierge $ai_concierge;

	public function __construct( Tripdesh_Booking $booking, Tripdesh_AI_Concierge $ai_concierge ) {
		$this->booking      = $booking;
		$this->ai_concierge = $ai_concierge;

		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	public function register_routes(): void {
		register_rest_route(
			self::NAMESPACE,
			'/booking',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'handle_booking' ),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			self::NAMESPACE,
			'/concierge',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'handle_concierge' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	public function handle_booking( WP_REST_Request $request ) {
		if ( $this->is_rate_limited( 'booking', 10, MINUTE_IN_SECONDS * 10 ) ) {
			return new WP_Error( 'tripdesh_rate_limited', __( 'Too many requests. Please try again shortly.', 'tripdesh' ), array( 'status' => 429 ) );
		}

		$params = $request->get_json_params();
		if ( ! is_array( $params ) ) {
			return new WP_Error( 'tripdesh_invalid_body', __( 'Invalid request body.', 'tripdesh' ), array( 'status' => 400 ) );
		}

		$result = $this->booking->create( $params );

		if ( isset( $result['error'] ) ) {
			return new WP_Error( 'tripdesh_booking_failed', $result['error'], array( 'status' => 400 ) );
		}

		return rest_ensure_response( $result );
	}

	public function handle_concierge( WP_REST_Request $request ) {
		if ( $this->is_rate_limited( 'concierge', 20, MINUTE_IN_SECONDS * 10 ) ) {
			return new WP_Error( 'tripdesh_rate_limited', __( 'Too many requests. Please try again shortly.', 'tripdesh' ), array( 'status' => 429 ) );
		}

		$params  = $request->get_json_params();
		$message = isset( $params['message'] ) ? sanitize_textarea_field( $params['message'] ) : '';

		if ( '' === trim( $message ) ) {
			return new WP_Error( 'tripdesh_empty_message', __( 'Please enter a message.', 'tripdesh' ), array( 'status' => 400 ) );
		}
		if ( strlen( $message ) > 2000 ) {
			return new WP_Error( 'tripdesh_message_too_long', __( 'Message is too long.', 'tripdesh' ), array( 'status' => 400 ) );
		}

		$language = isset( $params['language'] ) ? sanitize_text_field( $params['language'] ) : 'bn';
		$history  = isset( $params['history'] ) && is_array( $params['history'] ) ? $params['history'] : array();

		$result = $this->ai_concierge->respond( $message, $language, $history );

		return rest_ensure_response( $result );
	}

	private function is_rate_limited( string $bucket, int $max_requests, int $window_seconds ): bool {
		$ip  = $this->get_client_ip();
		$key = 'tripdesh_rl_' . $bucket . '_' . md5( $ip );

		$count = (int) get_transient( $key );
		if ( $count >= $max_requests ) {
			return true;
		}

		if ( 0 === $count ) {
			set_transient( $key, 1, $window_seconds );
		} else {
			set_transient( $key, $count + 1, $window_seconds );
		}

		return false;
	}

	private function get_client_ip(): string {
		$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '0.0.0.0';
		return $ip;
	}
}
