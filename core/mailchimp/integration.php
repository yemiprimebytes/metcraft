<?php

namespace MetCraft\Core\Mailchimp;

defined( 'ABSPATH' ) || exit;

/**
 * Mailchimp Integration for MetCraft forms.
 * Syncs form submissions to a Mailchimp audience/list.
 */
class Integration {

	/**
	 * Subscribe a user to a Mailchimp list.
	 *
	 * @param array $settings Form settings (mailchimp config).
	 * @param string $email Subscriber email.
	 * @param array $merge_fields Merge fields (FNAME, LNAME, etc.).
	 * @return array Result.
	 */
	public static function subscribe( $settings, $email, $merge_fields = [] ) {
		if ( empty( $settings['mailchimp_api_key'] ) || empty( $settings['mailchimp_list_id'] ) ) {
			return [ 'success' => false, 'error' => esc_html__( 'Mailchimp not configured.', 'metcraft' ) ];
		}

		$api_key = $settings['mailchimp_api_key'];
		$list_id = $settings['mailchimp_list_id'];

		// Extract datacenter from API key (usXX.api.mailchimp.com)
		$parts = explode( '-', $api_key );
		$dc = end( $parts );

		$url = 'https://' . $dc . '.api.mailchimp.com/3.0/lists/' . $list_id . '/members';

		$data = [
			'email_address' => $email,
			'status'        => 'subscribed',
			'merge_fields'  => $merge_fields,
		];

		$response = wp_remote_post( $url, [
			'method' => 'POST',
			'headers' => [
				'Authorization' => 'apikey ' . $api_key,
				'Content-Type'  => 'application/json',
			],
			'body' => wp_json_encode( $data ),
		] );

		if ( is_wp_error( $response ) ) {
			return [ 'success' => false, 'error' => $response->get_error_message() ];
		}

		$code = wp_remote_retrieve_response_code( $response );
		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( $code === 200 || $code === 201 ) {
			return [ 'success' => true ];
		}

		$error_msg = isset( $body['title'] ) ? $body['title'] : esc_html__( 'Mailchimp subscription failed.', 'metcraft' );
		return [ 'success' => false, 'error' => $error_msg ];
	}

}
