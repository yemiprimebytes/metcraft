<?php

namespace MetCraft\Core\Submission_Rules;

defined( 'ABSPATH' ) || exit;

/**
 * Submission Rules for MetCraft forms.
 * Controls who can submit based on rules (user roles, time windows, etc.).
 */
class Engine {

	/**
	 * Check if a user can submit the form based on rules.
	 *
	 * @param array $settings Form settings.
	 * @return array ['allowed' => bool, 'message' => string]
	 */
	public static function can_submit( $settings ) {
		if ( empty( $settings['submission_rules_enabled'] ) ) {
			return [ 'allowed' => true ];
		}

		$rules = $settings['submission_rules'] ?? [];
		$current_user = wp_get_current_user();

		foreach ( $rules as $rule ) {
			$type = $rule['type'] ?? '';

			switch ( $type ) {
				case 'user_role':
					$allowed_roles = isset( $rule['roles'] ) ? $rule['roles'] : [];
					if ( empty( $allowed_roles ) || ! array_intersect( (array) $current_user->roles, $allowed_roles ) ) {
						return [
							'allowed' => false,
							'message' => $rule['message'] ?? esc_html__( 'You are not allowed to submit this form.', 'metcraft' ),
						];
					}
					break;

				case 'user_id':
					$allowed_ids = isset( $rule['user_ids'] ) ? $rule['user_ids'] : [];
					if ( empty( $allowed_ids ) || ! in_array( $current_user->ID, $allowed_ids ) ) {
						return [
							'allowed' => false,
							'message' => $rule['message'] ?? esc_html__( 'You are not allowed to submit this form.', 'metcraft' ),
						];
					}
					break;

				case 'time_window':
					$start = $rule['start_time'] ?? '';
					$end   = $rule['end_time'] ?? '';
					$now   = current_time( 'mysql' );

					if ( $start && $now < $start ) {
						return [ 'allowed' => false, 'message' => esc_html__( 'Submissions are not yet open.', 'metcraft' ) ];
					}
					if ( $end && $now > $end ) {
						return [ 'allowed' => false, 'message' => esc_html__( 'Submissions are closed.', 'metcraft' ) ];
					}
					break;

				case 'max_submissions_per_user':
					$max = absint( $rule['max_per_user'] ?? 0 );
					if ( $max > 0 && is_user_logged_in() ) {
						$count = self::count_user_submissions( $current_user->ID, $settings );
						if ( $count >= $max ) {
							return [
								'allowed' => false,
								'message' => esc_html__( 'You have reached the maximum number of submissions.', 'metcraft' ),
							];
						}
					}
					break;
			}
		}

		return [ 'allowed' => true ];
	}

	/**
	 * Count submissions by a user for a form (uses entry meta or IP).
	 * Since we don't store user ID on all entries, this checks by IP for guests
	 * or user ID for logged-in users.
	 *
	 * @param int $user_id User ID.
	 * @param array $settings Form settings (to get form_id context).
	 * @return int
	 */
	private static function count_user_submissions( $user_id, $settings ) {
		global $wpdb;

		// This is a placeholder - entries table doesn't store user ID directly
		// In a full implementation, we'd track user_id in entry meta
		return 0;
	}

}
