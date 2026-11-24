<?php

namespace MetCraft\Core\Redirect;

defined( 'ABSPATH' ) || exit;

/**
 * Redirect After Submission for MetCraft forms.
 * Redirects user to a URL after successful form submission.
 */
class Manager {

	/**
	 * Get redirect URL from form settings.
	 *
	 * @param array $settings Form settings.
	 * @return string URL or empty.
	 */
	public static function get_url( $settings ) {
		return ! empty( $settings['redirect_url'] ) ? $settings['redirect_url'] : '';
	}

	/**
	 * Check if redirect is enabled.
	 *
	 * @param array $settings Form settings.
	 * @return bool
	 */
	public static function is_enabled( $settings ) {
		return ! empty( $settings['redirect_enabled'] );
	}

	/**
	 * Perform redirect (sends headers and exits).
	 *
	 * @param string $url Redirect URL.
	 */
	public static function go( $url ) {
		if ( empty( $url ) ) {
			return;
		}

		if ( headers_sent() ) {
			echo '<script>window.location.href = "' . esc_js( $url ) . '";</script>';
		} else {
			wp_safe_redirect( $url );
		}
		exit;
	}

}
