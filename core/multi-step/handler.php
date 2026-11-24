<?php

namespace MetCraft\Core\Multi_Step;

defined( 'ABSPATH' ) || exit;

/**
 * Multi-step Form handler for MetCraft.
 * Splits form fields into steps and manages step navigation.
 */
class Handler {

	/**
	 * Split fields into steps based on 'step' property.
	 *
	 * @param array $fields All form fields.
	 * @return array Fields grouped by step number.
	 */
	public static function group_fields_by_step( $fields ) {
		$steps = [];

		foreach ( $fields as $field ) {
			$step = isset( $field['step'] ) ? absint( $field['step'] ) : 1;
			if ( ! isset( $steps[ $step ] ) ) {
				$steps[ $step ] = [];
			}
			$steps[ $step ][] = $field;
		}

		ksort( $steps );

		return $steps;
	}

	/**
	 * Get the total number of steps.
	 *
	 * @param array $fields All form fields.
	 * @return int Total step count.
	 */
	public static function get_step_count( $fields ) {
		$steps = self::group_fields_by_step( $fields );
		return count( $steps );
	}

	/**
	 * Get fields for a specific step.
	 *
	 * @param array $fields All form fields.
	 * @param int $step Step number.
	 * @return array Fields for that step.
	 */
	public static function get_fields_for_step( $fields, $step ) {
		$steps = self::group_fields_by_step( $fields );
		return isset( $steps[ $step ] ) ? $steps[ $step ] : [];
	}

	/**
	 * Validate that all required fields in current step are filled.
	 *
	 * @param array $step_fields Fields for current step.
	 * @param array $submitted_values Submitted values.
	 * @return array Errors keyed by field key.
	 */
	public static function validate_step( $step_fields, $submitted_values ) {
		$errors = [];

		foreach ( $step_fields as $field ) {
			if ( ! empty( $field['required'] ) ) {
				$key = $field['key'] ?? '';
				if ( empty( $key ) ) {
					continue;
				}
				$value = isset( $submitted_values[ $key ] ) ? $submitted_values[ $key ] : '';

				if ( $value === '' || $value === [] || $value === null ) {
					$errors[ $key ] = sprintf(
						/* translators: %s is field label */
						esc_html__( '%s is required.', 'metcraft' ),
						$field['label'] ?? $key
					);
				}
			}
		}

		return $errors;
	}

}
