<?php

namespace MetCraft\Core\Integrations;

defined( 'ABSPATH' ) || exit;

/**
 * Google reCAPTCHA integration for MetCraft.
 */
class Google_Recaptcha {

	public static function init() {
		add_action( 'wp_ajax_metcraft_verify_recaptcha', [ self::class, 'verify_ajax' ] );
		add_action( 'wp_ajax_nopriv_metcraft_verify_recaptcha', [ self::class, 'verify_ajax' ] );
	}

	public static function verify_ajax() {
		check_ajax_referer( 'metcraft_recaptcha_nonce', 'nonce' );

		$settings = get_option( 'metcraft_settings', [] );
		$secret   = isset( $settings['mcf_recaptcha_secret_key'] ) ? $settings['mcf_recaptcha_secret_key'] : '';
		$version  = isset( $settings['mcf_recaptcha_version'] ) ? $settings['mcf_recaptcha_version'] : 'recaptcha-v2';

		if ( empty( $secret ) ) {
			wp_send_json_error( __( 'reCAPTCHA not configured.', 'metcraft' ) );
		}

		$token = sanitize_text_field( $_POST['token'] ?? '' );
		if ( empty( $token ) ) {
			wp_send_json_error( __( 'Missing reCAPTCHA token.', 'metcraft' ) );
		}

		if ( $version === 'recaptcha-v3' ) {
			$response = wp_remote_get( 'https://www.google.com/recaptcha/api/siteverify', [
				'body' => [
					'secret'   => $secret,
					'response' => $token,
				],
			] );
		} else {
			$response = wp_remote_get( 'https://www.google.com/recaptcha/api/siteverify', [
				'body' => [
					'secret'   => $secret,
					'response' => $token,
				],
			] );
		}

		$result = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( ! empty( $result['success'] ) ) {
			wp_send_json_success();
		} else {
			wp_send_json_error( __( 'reCAPTCHA verification failed.', 'metcraft' ) );
		}
	}

	public static function get_html( $form_id, $widget_id = '' ) {
		$settings = get_option( 'metcraft_settings', [] );
		$site_key = isset( $settings['mcf_recaptcha_site_key'] ) ? $settings['mcf_recaptcha_site_key'] : '';
		$version  = isset( $settings['mcf_recaptcha_version'] ) ? $settings['mcf_recaptcha_version'] : 'recaptcha-v2';

		if ( empty( $site_key ) ) {
			return '';
		}

		if ( $version === 'recaptcha-v3' ) {
			return sprintf(
				'<div class="grecaptcha-v3" data-sitekey="%s" data-action="form_submit_%d"></div>',
				esc_attr( $site_key ),
				absint( $form_id )
			);
		}

		return sprintf(
			'<div class="g-recaptcha" data-sitekey="%s"></div>',
			esc_attr( $site_key )
		);
	}

}
