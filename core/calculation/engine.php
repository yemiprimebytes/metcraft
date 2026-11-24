<?php

namespace MetCraft\Core\Calculation;

defined( 'ABSPATH' ) || exit;

/**
 * Calculation Engine for MetCraft forms.
 * Evaluates mathematical expressions using field values.
 */
class Engine {

	/**
	 * Evaluate a calculation expression.
	 *
	 * Supports: +, -, *, /, (), and field references like {field_key}
	 *
	 * @param string $expression The calculation expression.
	 * @param array $field_values Map of field_key => value.
	 * @return float|string Result or error message.
	 */
	public static function evaluate( $expression, $field_values = [] ) {
		if ( empty( $expression ) ) {
			return 0;
		}

		// Replace field references {field_key} with values
		$evaluated = preg_replace_callback(
			'/\{([^}]+)\}/',
			function ( $matches ) use ( $field_values ) {
				$key = $matches[1];
				return isset( $field_values[ $key ] ) ? $field_values[ $key ] : '0';
			},
			$expression
		);

		// Remove anything that isn't a number, operator, or parenthesis
		$evaluated = preg_replace( '/[^0-9.\+\-\*\/\(\)\s]/', '', $evaluated );

		if ( ! preg_match( '/^[0-9.\+\-\*\/\(\)\s]+$/', $evaluated ) ) {
			return 0;
		}

		// Use eval safely with only math
		$result = @eval( 'return ' . $evaluated . ';' );

		if ( $result === false || ! is_numeric( $result ) ) {
			return 0;
		}

		return (float) $result;
	}

	/**
	 * Get all calculation fields from form fields.
	 *
	 * @param array $fields All form fields.
	 * @return array Fields of type 'calculation'.
	 */
	public static function get_calculation_fields( $fields ) {
		$calc_fields = [];

		foreach ( $fields as $field ) {
			if ( isset( $field['type'] ) && $field['type'] === 'calculation' ) {
				$calc_fields[] = $field;
			}
		}

		return $calc_fields;
	}

}
