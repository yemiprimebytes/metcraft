<?php

namespace MetCraft\Core\Entries;

defined( 'ABSPATH' ) || exit;

/**
 * Validation for entry data.
 */
class Validation {

	public static function validate_required( array $data, array $fields ) {
		$errors = [];

		foreach ( $fields as $field ) {
			if ( ! empty( $field['required'] ) && ( isset( $data[ $field['key'] ] ) && ( $data[ $field['key'] ] === '' || $data[ $field['key'] ] === [] ) ) ) {
				$errors[ $field['key'] ] = sprintf( __( '%s is required.', 'metcraft' ), $field['label'] ?? $field['key'] );
			}
		}

		return $errors;
	}

	public static function validate_email( $value ) {
		if ( ! is_email( $value ) ) {
			return __( 'Please enter a valid email address.', 'metcraft' );
		}
		return '';
	}

}
