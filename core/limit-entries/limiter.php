<?php

namespace MetCraft\Core\Limit_Entries;

defined( 'ABSPATH' ) || exit;

/**
 * Limit Entries for MetCraft forms.
 * Caps the total number of form submissions.
 */
class Limiter {

	/**
	 * Check if the form has reached its entry limit.
	 *
	 * @param int $form_id Form ID.
	 * @param array $settings Form settings.
	 * @return array ['allowed' => bool, 'message' => string]
	 */
	public static function can_submit( $form_id, $settings ) {
		if ( empty( $settings['limit_entries_enabled'] ) ) {
			return [ 'allowed' => true ];
		}

		$limit = absint( $settings['limit_entries_max'] ?? 0 );
		if ( $limit <= 0 ) {
			return [ 'allowed' => true ];
		}

		$form_id = absint( $form_id );
		$entries = \MetCraft\Core\Entries\Base::instance()->get_entries( $form_id );
		$count   = count( $entries );

		if ( $count >= $limit ) {
			return [
				'allowed'  => false,
				'message'  => $settings['limit_entries_message'] ?? esc_html__( 'This form has reached its submission limit.', 'metcraft' ),
			];
		}

		return [ 'allowed' => true ];
	}

}
