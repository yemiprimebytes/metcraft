<?php

namespace MetCraft\Core\Required_Login;

defined( 'ABSPATH' ) || exit;

/**
 * Required Login check for MetCraft forms.
 * Restricts form access to logged-in users.
 */
class Checker {

	/**
	 * Check if current user can access the form.
	 *
	 * @param array $settings Form settings.
	 * @return bool True if access allowed.
	 */
	public static function can_access( $settings ) {
		if ( empty( $settings['require_login'] ) ) {
			return true;
		}

		return is_user_logged_in();
	}

	/**
	 * Get the login required message.
	 *
	 * @param array $settings Form settings.
	 * @return string Message to show.
	 */
	public static function get_message( $settings ) {
		return $settings['require_login_message'] ?? esc_html__( 'You must be logged in to submit this form.', 'metcraft' );
	}

	/**
	 * Get the login URL.
	 *
	 * @return string
	 */
	public static function get_login_url() {
		return wp_login_url( get_permalink() );
	}

}
