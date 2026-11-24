<?php

namespace MetCraft\Core\Webhook;

defined( 'ABSPATH' ) || exit;

/**
 * Webhook delivery for MetCraft form submissions.
 * Sends form data to a third-party URL via POST.
 */
class Delivery {

	/**
	 * Send webhook notification.
	 *
	 * @param array $settings Form settings.
	 * @param int $form_id Form ID.
	 * @param array $entry_data Submitted data.
	 * @param int $entry_id Entry ID.
	 * @return array Result.
	 */
	public static function send( $settings, $form_id, $entry_data, $entry_id ) {
		if ( empty( $settings['webhook_enabled'] ) ) {
			return [ 'sent' => false ];
		}

		$url = $settings['webhook_url'] ?? '';
		if ( empty( $url ) ) {
			return [ 'sent' => false, 'error' => esc_html__( 'No webhook URL configured.', 'metcraft' ) ];
		}

		$payload = [
			'form_id'   => $form_id,
			'entry_id'  => $entry_id,
			'data'      => $entry_data,
			'timestamp' => current_time( 'mysql' ),
		];

		// Include secret if configured
		$secret = $settings['webhook_secret'] ?? '';
		$headers = [ 'Content-Type' => 'application/json' ];
		if ( ! empty( $secret ) ) {
			$headers['X-MetCraft-Signature'] = hash_hmac( 'sha256', wp_json_encode( $payload ), $secret );
		}

		$response = wp_remote_post( $url, [
			'method'  => 'POST',
			'headers' => $headers,
			'body'    => wp_json_encode( $payload ),
			'timeout' => 15,
		] );

		if ( is_wp_error( $response ) ) {
			return [ 'sent' => false, 'error' => $response->get_error_message() ];
		}

		return [ 'sent' => true, 'status_code' => wp_remote_retrieve_response_code( $response ) ];
	}

}
