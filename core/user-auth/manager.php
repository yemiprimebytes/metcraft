<?php

namespace MetCraft\User_Auth;

defined( 'ABSPATH' ) || exit;

/**
 * User Login & Registration for MetCraft forms.
 * Creates WordPress user accounts from form submissions.
 */
class Manager {

	/**
	 * Check if user registration is enabled for a form.
	 *
	 * @param array $settings Form settings.
	 * @return bool
	 */
	public static function is_registration_enabled( $settings ) {
		return ! empty( $settings['user_registration_enabled'] );
	}

	/**
	 * Check if user login is enabled for a form.
	 *
	 * @param array $settings Form settings.
	 * @return bool
	 */
	public static function is_login_enabled( $settings ) {
		return ! empty( $settings['user_login_enabled'] );
	}

	/**
	 * Register a new user from form data.
	 *
	 * @param array $settings Form settings.
	 * @param array $data Form data.
	 * @return int|WP_User|WP_Error New user ID or error.
	 */
	public static function register_user( $settings, $data ) {
		$email_field = $settings['user_registration_email_field'] ?? 'email';
		$password_field = $settings['user_registration_password_field'] ?? 'password';
		$username_field = $settings['user_registration_username_field'] ?? 'username';
		$role = $settings['user_registration_role'] ?? 'subscriber';

		$email = isset( $data[ $email_field ] ) ? $data[ $email_field ] : '';
		$password = isset( $data[ $password_field ] ) ? $data[ $password_field ] : '';
		$username = isset( $data[ $username_field ] ) ? $data[ $username_field ] : $email;

		if ( ! is_email( $email ) ) {
			return new \WP_Error( 'invalid_email', esc_html__( 'Invalid email address.', 'metcraft' ) );
		}

		if ( username_exists( $username ) || email_exists( $email ) ) {
			return new \WP_Error( 'user_exists', esc_html__( 'User already exists.', 'metcraft' ) );
		}

		$user_id = wp_create_user( $username, $password, $email );

		if ( is_wp_error( $user_id ) ) {
			return $user_id;
		}

		$wp_user = new \WP_User( $user_id );
		$wp_user->set_role( $role );

		return $user_id;
	}

	/**
	 * Authenticate and log in a user from form data.
	 *
	 * @param array $data Form data.
	 * @param array $settings Form settings.
	 * @return bool|WP_Error
	 */
	public static function login_user( $data, $settings ) {
		$username_field = $settings['user_login_username_field'] ?? 'username';
		$password_field = $settings['user_login_password_field'] ?? 'password';

		$username = isset( $data[ $username_field ] ) ? $data[ $username_field ] : '';
		$password = isset( $data[ $password_field ] ) ? $data[ $password_field ] : '';

		if ( empty( $username ) || empty( $password ) ) {
			return new \WP_Error( 'empty_credentials', esc_html__( 'Username and password are required.', 'metcraft' ) );
		}

		$creds = [
			'user_login'    => $username,
			'user_password' => $password,
		];

		$user = wp_signon( $creds, false );

		if ( is_wp_error( $user ) ) {
			return $user;
		}

		return true;
	}

}
