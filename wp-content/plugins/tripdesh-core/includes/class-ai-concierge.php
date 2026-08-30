<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AI Travel Concierge (brief §12, §20). One provider-agnostic endpoint
 * standing in for the concierge/destination/package orchestration
 * described in the brief — see ARCHITECTURE.md §4 for why this is one
 * endpoint instead of twelve services in v1.
 *
 * Never books or charges anything: it only returns recommendations. Any
 * booking goes through Tripdesh_Booking, which is a human-in-the-loop
 * flow by construction (brief §5).
 */
class Tripdesh_AI_Concierge {

	private Tripdesh_Settings $settings;

	public function __construct( Tripdesh_Settings $settings ) {
		$this->settings = $settings;
	}

	public function respond( string $message, string $language, array $history ): array {
		$language  = 'bn' === $language ? 'bn' : 'en';
		$api_key   = $this->settings->get( 'ai_api_key' );

		if ( '' === trim( (string) $api_key ) ) {
			return array(
				'reply'    => $this->fallback_message( $language ),
				'fallback' => true,
			);
		}

		$provider = $this->settings->get( 'ai_provider', 'anthropic' );
		$system   = $this->build_system_prompt( $language );

		try {
			$reply = 'openai' === $provider
				? $this->call_openai( $system, $message, $history, $api_key )
				: $this->call_anthropic( $system, $message, $history, $api_key );
		} catch ( Exception $e ) {
			return array(
				'reply'    => $this->fallback_message( $language ),
				'fallback' => true,
			);
		}

		return array(
			'reply'    => $reply,
			'fallback' => false,
		);
	}

	private function fallback_message( string $language ): string {
		if ( 'bn' === $language ) {
			return __( 'ধন্যবাদ! আমাদের AI ট্রাভেল অ্যাসিস্ট্যান্ট এখনো সেটআপ করা হয়নি। অনুগ্রহ করে আমাদের গন্তব্য ও ট্যুর পেজ ঘুরে দেখুন, অথবা সরাসরি আমাদের সাথে যোগাযোগ করুন।', 'tripdesh' );
		}
		return __( "Thanks for reaching out! Our AI travel assistant isn't fully set up yet. In the meantime, browse our Destinations and Tours pages, or contact us directly and our team will help plan your trip.", 'tripdesh' );
	}

	/**
	 * Grounds the model in what's actually bookable so it doesn't invent
	 * packages that don't exist (ARCHITECTURE.md §4).
	 */
	private function build_system_prompt( string $language ): string {
		$destinations = get_posts(
			array(
				'post_type'      => 'destination',
				'posts_per_page' => 15,
				'post_status'    => 'publish',
			)
		);
		$packages = get_posts(
			array(
				'post_type'      => 'tour_package',
				'posts_per_page' => 20,
				'post_status'    => 'publish',
			)
		);

		$destination_lines = array();
		foreach ( $destinations as $d ) {
			$budget               = get_post_meta( $d->ID, '_tripdesh_estimated_budget', true );
			$destination_lines[] = '- ' . $d->post_title . ( $budget ? " (approx. budget: {$budget} BDT/person)" : '' );
		}

		$package_lines = array();
		foreach ( $packages as $p ) {
			$days  = get_post_meta( $p->ID, '_tripdesh_duration_days', true );
			$price = get_post_meta( $p->ID, '_tripdesh_price', true );
			$package_lines[] = '- ' . $p->post_title . ( $days ? " ({$days} days" : '' ) . ( $price ? ", {$price} BDT/person)" : ( $days ? ')' : '' ) );
		}

		$language_instruction = 'bn' === $language
			? 'Respond in natural, friendly Bengali (বাংলা) unless the customer writes in English.'
			: 'Respond in English unless the customer writes in Bengali, in which case reply in Bengali.';

		$prompt = "You are the Tripdesh Travel Concierge, an AI assistant for a Bangladesh domestic travel agency. "
			. "You help customers pick destinations, build itineraries, and estimate budgets in BDT. "
			. $language_instruction . " "
			. "Only recommend destinations and tour packages from the lists below — never invent packages, prices, or hotels that aren't listed. "
			. "If nothing on the list fits, say so honestly and suggest contacting the team. "
			. "You never confirm a booking or take payment yourself — always end a recommendation by inviting the customer to use the booking form on the relevant tour/destination page. "
			. "Keep replies concise and practical.\n\n"
			. "Available destinations:\n" . ( $destination_lines ? implode( "\n", $destination_lines ) : '(none published yet)' ) . "\n\n"
			. "Available tour packages:\n" . ( $package_lines ? implode( "\n", $package_lines ) : '(none published yet)' );

		return $prompt;
	}

	private function call_anthropic( string $system, string $message, array $history, string $api_key ): string {
		$messages = $this->build_messages( $history, $message );
		$model    = $this->settings->get( 'ai_model' ) ?: 'claude-sonnet-5';

		$response = wp_remote_post(
			'https://api.anthropic.com/v1/messages',
			array(
				'timeout' => 20,
				'headers' => array(
					'x-api-key'         => $api_key,
					'anthropic-version' => '2023-06-01',
					'content-type'      => 'application/json',
				),
				'body'    => wp_json_encode(
					array(
						'model'      => $model,
						'max_tokens' => 600,
						'system'     => $system,
						'messages'   => $messages,
					)
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			throw new Exception( $response->get_error_message() );
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( empty( $body['content'][0]['text'] ) ) {
			throw new Exception( 'Unexpected Anthropic API response' );
		}

		return sanitize_textarea_field( $body['content'][0]['text'] );
	}

	private function call_openai( string $system, string $message, array $history, string $api_key ): string {
		$messages = array_merge(
			array( array( 'role' => 'system', 'content' => $system ) ),
			$this->build_messages( $history, $message )
		);
		$model = $this->settings->get( 'ai_model' ) ?: 'gpt-4o-mini';

		$response = wp_remote_post(
			'https://api.openai.com/v1/chat/completions',
			array(
				'timeout' => 20,
				'headers' => array(
					'Authorization' => 'Bearer ' . $api_key,
					'content-type'  => 'application/json',
				),
				'body'    => wp_json_encode(
					array(
						'model'    => $model,
						'messages' => $messages,
						'max_tokens' => 600,
					)
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			throw new Exception( $response->get_error_message() );
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( empty( $body['choices'][0]['message']['content'] ) ) {
			throw new Exception( 'Unexpected OpenAI API response' );
		}

		return sanitize_textarea_field( $body['choices'][0]['message']['content'] );
	}

	/**
	 * Caps history to the last 8 turns to keep requests small/cheap.
	 */
	private function build_messages( array $history, string $message ): array {
		$messages = array();
		$history  = array_slice( $history, -8 );

		foreach ( $history as $turn ) {
			if ( ! isset( $turn['role'], $turn['content'] ) || ! in_array( $turn['role'], array( 'user', 'assistant' ), true ) ) {
				continue;
			}
			$messages[] = array(
				'role'    => $turn['role'],
				'content' => sanitize_textarea_field( $turn['content'] ),
			);
		}

		$messages[] = array( 'role' => 'user', 'content' => $message );

		return $messages;
	}
}
