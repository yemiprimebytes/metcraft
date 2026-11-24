<?php

namespace MetCraft\Core\Conditional_Logic;

defined( 'ABSPATH' ) || exit;

/**
 * Conditional Logic Engine for MetCraft forms.
 * Evaluates conditions to show/hide fields dynamically.
 */
class Engine {

	/**
	 * Evaluate conditions for a field and return whether it should be visible.
	 *
	 * @param array $field Field configuration with 'conditions' key.
	 * @param array $submitted_values Currently submitted field values.
	 * @return bool True if field should be shown.
	 */
	public static function is_field_visible( $field, $submitted_values = [] ) {
		$conditions = isset( $field['conditions'] ) && is_array( $field['conditions'] ) ? $field['conditions'] : [];

		if ( empty( $conditions ) ) {
			return true; // No conditions = always visible
		}

		$condition_type = isset( $conditions['type'] ) ? $conditions['type'] : 'any'; // 'any' or 'all'
		$rules = isset( $conditions['rules'] ) && is_array( $conditions['rules'] ) ? $conditions['rules'] : [];

		if ( empty( $rules ) ) {
			return true;
		}

		$results = [];

		foreach ( $rules as $rule ) {
			$field_key    = $rule['field_key'] ?? '';
			$operator     = $rule['operator'] ?? 'equals';
			$rule_value   = $rule['value'] ?? '';
			$actual_value = isset( $submitted_values[ $field_key ] ) ? $submitted_values[ $field_key ] : '';

			$results[] = self::evaluate_rule( $actual_value, $operator, $rule_value );
		}

		if ( $condition_type === 'all' ) {
			return ! in_array( false, $results, true );
		}

		return in_array( true, $results, true );
	}

	/**
	 * Evaluate a single condition rule.
	 */
	private static function evaluate_rule( $actual, $operator, $expected ) {
		switch ( $operator ) {
			case 'equals':
				return $actual == $expected;
			case 'not_equals':
				return $actual != $expected;
			case 'contains':
				return is_string( $actual ) && strpos( $actual, $expected ) !== false;
			case 'not_contains':
				return is_string( $actual ) && strpos( $actual, $expected ) === false;
			case 'greater_than':
				return is_numeric( $actual ) && is_numeric( $expected ) && $actual > $expected;
			case 'less_than':
				return is_numeric( $actual ) && is_numeric( $expected ) && $actual < $expected;
			case 'is_empty':
				return $actual === '' || $actual === [] || $actual === null;
			case 'is_not_empty':
				return $actual !== '' && $actual !== [] && $actual !== null;
			default:
				return true;
		}
	}

	/**
	 * Get all fields that should be visible given submitted values.
	 *
	 * @param array $fields All form fields.
	 * @param array $submitted_values Current values.
	 * @return array Filtered fields.
	 */
	public static function filter_visible_fields( $fields, $submitted_values = [] ) {
		$visible = [];

		foreach ( $fields as $field ) {
			if ( self::is_field_visible( $field, $submitted_values ) ) {
				$visible[] = $field;
			}
		}

		return $visible;
	}

}
