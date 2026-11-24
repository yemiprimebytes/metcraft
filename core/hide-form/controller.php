<?php

namespace MetCraft\Core\Hide_Form;

defined( 'ABSPATH' ) || exit;

/**
 * Hide Form After Submission for MetCraft.
 * Controls whether form is hidden after successful submission.
 */
class Controller {

	/**
	 * Check if form should be hidden after submission.
	 *
	 * @param array $settings Form settings.
	 * @return bool
	 */
	public static function should_hide( $settings ) {
		return ! empty( $settings['hide_form_after_submission'] );
	}

	/**
	 * Get the message to show instead of form.
	 *
	 * @param array $settings Form settings.
	 * @return string
	 */
	public static function get_hidden_message( $settings ) {
		return $settings['hide_form_message'] ?? esc_html__( 'Thank you for your submission!', 'metcraft' );
	}

}
