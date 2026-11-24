<?php

namespace MetCraft\Core\Confirmation_Email;

defined( 'ABSPATH' ) || exit;

/**
 * User Confirmation Email for MetCraft forms.
 * Sends an automatic email to the form submitter.
 */
class Sender {

	/**
	 * Send confirmation email to form submitter.
	 *
	 * @param array $settings Form settings.
	 * @param array $entry_data Submitted form data.
	 * @param int $form_id Form ID.
	 * @return bool
	 */
	public static function send( $settings, $entry_data, $form_id ) {
		if ( empty( $settings['confirmation_email_enabled'] ) ) {
			return false;
		}

		$email_field = $settings['confirmation_email_field'] ?? '';
		$to = isset( $entry_data[ $email_field ] ) ? $entry_data[ $email_field ] : '';

		if ( empty( $to ) || ! is_email( $to ) ) {
			return false;
		}

		$subject = $settings['confirmation_email_subject'] ?? esc_html__( 'Thank you for your submission', 'metcraft' );
		$message = $settings['confirmation_email_message'] ?? esc_html__( 'Thank you for submitting the form.', 'metcraft' );

		// Replace field placeholders in message
		$message = self::replace_placeholders( $message, $entry_data );
		$subject = self::replace_placeholders( $subject, $entry_data );

		$headers = [
			'Content-Type: text/html; charset=UTF-8',
			'From: ' . get_option( 'blogname' ) . ' <' . get_option( 'admin_email' ) . '>',
		];

		return wp_mail( $to, $subject, $message, $headers );
	}

	/**
	 * Replace {field_key} placeholders with actual values.
	 *
	 * @param string $text Text with placeholders.
	 * @param array $data Form data.
	 * @return string
	 */
	private static function replace_placeholders( $text, $data ) {
		foreach ( $data as $key => $value ) {
			if ( is_array( $value ) ) {
				$value = implode( ', ', $value );
			}
			$text = str_replace( '{' . $key . '}', esc_html( $value ), $text );
		}
		return $text;
	}

}
