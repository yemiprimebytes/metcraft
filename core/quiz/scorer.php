<?php

namespace MetCraft\Core\Quiz;

defined( 'ABSPATH' ) || exit;

/**
 * Quiz & Survey scoring engine for MetCraft.
 * Calculates scores and determines results based on field values.
 */
class Scorer {

	/**
	 * Calculate total score from quiz fields.
	 *
	 * @param array $fields All form fields (quiz type have 'points' and 'correct_value').
	 * @param array $submitted_values Submitted field values.
	 * @return array Score data with total, max, percentage.
	 */
	public static function calculate_score( $fields, $submitted_values ) {
		$total_score = 0;
		$max_score   = 0;
		$field_scores = [];

		foreach ( $fields as $field ) {
			if ( ! isset( $field['quiz'] ) || ! $field['quiz'] ) {
				continue;
			}

			$points    = absint( $field['points'] ?? 0 );
			$key       = $field['key'] ?? '';
			$correct   = $field['correct_value'] ?? '';
			$submitted = isset( $submitted_values[ $key ] ) ? $submitted_values[ $key ] : '';

			$max_score += $points;
			$earned    = 0;

			if ( $submitted !== '' && $submitted === $correct ) {
				$earned = $points;
			}

			$total_score += $earned;

			$field_scores[] = [
				'key'     => $key,
				'label'   => $field['label'] ?? '',
				'points'  => $points,
				'earned'  => $earned,
				'correct' => $correct,
				'submitted'=> $submitted,
				'is_correct'=> $submitted === $correct,
			];
		}

		$percentage = $max_score > 0 ? round( ( $total_score / $max_score ) * 100, 2 ) : 0;

		return [
			'total_score' => $total_score,
			'max_score'   => $max_score,
			'percentage'  => $percentage,
			'field_scores'=> $field_scores,
		];
	}

	/**
	 * Determine result message based on percentage and result rules.
	 *
	 * @param float $percentage Score percentage.
	 * @param array $results Result rules with 'min_percentage' and 'message'.
	 * @return string Result message.
	 */
	public static function get_result_message( $percentage, $results = [] ) {
		if ( empty( $results ) ) {
			return sprintf(
				/* translators: %s is percentage */
				esc_html__( 'You scored %s%%.', 'metcraft' ),
				$percentage
			);
		}

		// Sort by min_percentage descending
		usort( $results, function ( $a, $b ) {
			return absint( $b['min_percentage'] ?? 0 ) <=> absint( $a['min_percentage'] ?? 0 );
		} );

		foreach ( $results as $result ) {
			$min = absint( $result['min_percentage'] ?? 0 );
			if ( $percentage >= $min ) {
				return $result['message'] ?? '';
			}
		}

		return $results[0]['message'] ?? '';
	}

}
