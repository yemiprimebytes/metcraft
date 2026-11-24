<?php

namespace MetCraft\Core\Email_Verification;

defined( 'ABSPATH' ) || exit;

/**
 * Email Verification for MetCraft forms.
 * Verifies submitter email before processing the submission.
 */
class Verifier {

	/**
	 * Check if email verification is required for a form.
	 *
	 * @param array $settings Form settings.
	 * @return bool
	 */
	public static function is_required( $settings ) {
		return ! empty( $settings['email_verification_enabled'] );
	}

	/**
	 * Generate a verification token.
	 *
	 * @param string $email Email address.
	 * @return string Token.
	 */
	public static function generate_token( $email ) {
		return wp_hash( $email . current_time( 'mysql' ) . wp_rand() );
	}

	/**
	 * Store verification data as a transient.
	 *
	 * @param string $token Verification token.
	 * @param array $data Form data to verify.
	 * @param int $expiration Seconds until expiry.
	 */
	public static function store( $token, $data, $expiration = 3600 ) {
		set_transient( 'metcraft_email_verify_' . $token, $data, $expiration );
	}

	/**
	 * Retrieve and delete verification data.
	 *
	 * @param string $token Verification token.
	 * @return array|false
	 */
	public static function consume( $token ) {
		$data = get_transient( 'metcraft_email_verify_' . $token );
		if ( $data !== false ) {
			delete_transient( 'metcraft_email_verify_' . $token );
		}
		return $data;
	}

	/**
	 * Send verification email.
	 *
	 * @param string $to Email address.
	 * @param string $token Verification token.
	 * @param int $form_id Form ID.
	 * @return bool
	 */
	public static function send_verification_email( $to, $token, $form_id ) {
		$verify_url = add_query_arg( [
			'mcf_verify'   => $token,
			'mcf_form_id'  => $form_id,
		], home_url() );

		$subject = esc_html__( 'Verify your email address', 'metcraft' );
		$message = sprintf(
			/* translators: %s is verification URL */
			esc_html__( 'Please click the following link to verify your email and complete your submission: %s', 'metcraft' ),
			esc_url( $verify_url )
		);

		$headers = [ 'Content-Type: text/plain; charset=UTF-8' ];

		return wp_mail( $to, $subject, $message, $headers );
	}

}
