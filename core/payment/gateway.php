<?php

namespace MetCraft\Core\Payment;

defined( 'ABSPATH' ) || exit;

/**
 * Payment integration for MetCraft forms.
 * Supports Stripe and PayPal payment collection.
 */
class Gateway {

	/**
	 * Get payment settings for a form.
	 *
	 * @param array $settings Form settings.
	 * @return array Payment configuration.
	 */
	public static function get_config( $settings ) {
		return [
			'enabled'         => ! empty( $settings['payment_enabled'] ),
			'gateway'         => $settings['payment_gateway'] ?? 'stripe', // 'stripe' or 'paypal'
			'amount_field'    => $settings['payment_amount_field'] ?? '',
			'currency'        => $settings['payment_currency'] ?? 'USD',
			'stripe_secret_key'=> $settings['stripe_secret_key'] ?? '',
			'stripe_publishable_key'=> $settings['stripe_publishable_key'] ?? '',
			'paypal_client_id'=> $settings['paypal_client_id'] ?? '',
			'paypal_secret'   => $settings['paypal_secret'] ?? '',
			'paypal_sandbox'  => ! empty( $settings['paypal_sandbox'] ),
			'description'     => $settings['payment_description'] ?? '',
		];
	}

	/**
	 * Process Stripe payment.
	 *
	 * @param array $config Payment config.
	 * @param float $amount Amount to charge.
	 * @param string $payment_token Payment method token from frontend.
	 * @return array Result with success/error.
	 */
	public static function process_stripe( $config, $amount, $payment_token ) {
		if ( empty( $config['stripe_secret_key'] ) ) {
			return [ 'success' => false, 'error' => esc_html__( 'Stripe not configured.', 'metcraft' ) ];
		}

		$amount_cents = absint( $amount * 100 );

		$response = wp_remote_post( 'https://api.stripe.com/v1/payment_intents', [
			'method' => 'POST',
			'headers' => [
				'Authorization' => 'Bearer ' . $config['stripe_secret_key'],
				'Content-Type'  => 'application/x-www-form-urlencoded',
			],
			'body' => [
				'amount'         => $amount_cents,
				'currency'       => strtolower( $config['currency'] ),
				'payment_method' => $payment_token,
				'confirm'        => 'true',
			],
		] );

		if ( is_wp_error( $response ) ) {
			return [ 'success' => false, 'error' => $response->get_error_message() ];
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( isset( $body['status'] ) && $body['status'] === 'succeeded' ) {
			return [ 'success' => true, 'transaction_id' => $body['id'] ?? '' ];
		}

		$error_msg = isset( $body['error']['message'] ) ? $body['error']['message'] : esc_html__( 'Payment failed.', 'metcraft' );
		return [ 'success' => false, 'error' => $error_msg ];
	}

	/**
	 * Process PayPal payment (v2 checkout).
	 *
	 * @param array $config Payment config.
	 * @param float $amount Amount to charge.
	 * @return array Result with approval URL or error.
	 */
	public static function process_paypal( $config, $amount ) {
		if ( empty( $config['paypal_client_id'] ) || empty( $config['paypal_secret'] ) ) {
			return [ 'success' => false, 'error' => esc_html__( 'PayPal not configured.', 'metcraft' ) ];
		}

		$base = $config['paypal_sandbox'] ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com';

		// Get access token
		$auth = wp_remote_post( $base . '/v1/oauth2/token', [
			'method' => 'POST',
			'headers' => [ 'Accept' => 'application/json', 'Accept-Language' => 'en_US' ],
			'body' => [ 'grant_type' => 'client_credentials' ],
			'auth' => [ $config['paypal_client_id'], $config['paypal_secret'] ],
		] );

		if ( is_wp_error( $auth ) ) {
			return [ 'success' => false, 'error' => $auth->get_error_message() ];
		}

		$auth_body = json_decode( wp_remote_retrieve_body( $auth ), true );
		$access_token = $auth_body['access_token'] ?? '';

		if ( empty( $access_token ) ) {
			return [ 'success' => false, 'error' => esc_html__( 'PayPal authentication failed.', 'metcraft' ) ];
		}

		// Create order
		$order_data = [
			'intent' => 'CAPTURE',
			'purchase_units' => [[
				'amount' => [
					'currency_code' => $config['currency'],
					'value'         => number_format( (float) $amount, 2, '.', '' ),
				],
			]],
			'application_context' => [
				'return_url' => add_query_arg( 'mcf_payment_status', 'success', home_url() ),
				'cancel_url' => add_query_arg( 'mcf_payment_status', 'cancel', home_url() ),
			],
		];

		$order = wp_remote_post( $base . '/v2/checkout/orders', [
			'method' => 'POST',
			'headers' => [
				'Authorization' => 'Bearer ' . $access_token,
				'Content-Type'  => 'application/json',
			],
			'body' => wp_json_encode( $order_data ),
		] );

		if ( is_wp_error( $order ) ) {
			return [ 'success' => false, 'error' => $order->get_error_message() ];
		}

		$order_body = json_decode( wp_remote_retrieve_body( $order ), true );

		if ( isset( $order_body['id'] ) ) {
			// Find approval URL
			$approval_url = '';
			foreach ( ( $order_body['links'] ?? [] ) as $link ) {
				if ( $link['rel'] === 'approve' ) {
					$approval_url = $link['href'];
					break;
				}
			}

			return [
				'success'    => true,
				'order_id'   => $order_body['id'],
				'approval_url'=> $approval_url,
			];
		}

		return [ 'success' => false, 'error' => esc_html__( 'PayPal order creation failed.', 'metcraft' ) ];
	}

	/**
	 * Capture PayPal payment after approval.
	 *
	 * @param array $config Payment config.
	 * @param string $order_id PayPal order ID.
	 * @return array Result.
	 */
	public static function capture_paypal( $config, $order_id ) {
		$base = $config['paypal_sandbox'] ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com';

		$auth = wp_remote_post( $base . '/v1/oauth2/token', [
			'method' => 'POST',
			'headers' => [ 'Accept' => 'application/json' ],
			'body' => [ 'grant_type' => 'client_credentials' ],
			'auth' => [ $config['paypal_client_id'], $config['paypal_secret'] ],
		] );

		if ( is_wp_error( $auth ) ) {
			return [ 'success' => false, 'error' => $auth->get_error_message() ];
		}

		$auth_body = json_decode( wp_remote_retrieve_body( $auth ), true );
		$access_token = $auth_body['access_token'] ?? '';

		$capture = wp_remote_post( $base . '/v2/checkout/orders/' . $order_id . '/capture', [
			'method' => 'POST',
			'headers' => [
				'Authorization' => 'Bearer ' . $access_token,
				'Content-Type'  => 'application/json',
			],
		] );

		if ( is_wp_error( $capture) ) {
			return [ 'success' => false, 'error' => $capture->get_error_message() ];
		}

		$body = json_decode( wp_remote_retrieve_body( $capture ), true );

		if ( isset( $body['status'] ) && $body['status'] === 'COMPLETED' ) {
			return [ 'success' => true, 'transaction_id' => $body['id'] ?? '' ];
		}

		return [ 'success' => false, 'error' => esc_html__( 'PayPal capture failed.', 'metcraft' ) ];
	}

}
