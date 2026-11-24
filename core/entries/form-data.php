<?php

namespace MetCraft\Core\Entries;

defined( 'ABSPATH' ) || exit;

/**
 * Form data handling for entries.
 */
class Form_Data {

	public static function sanitize_data( array $data, array $fields ) {
		$sanitized = [];
		foreach ( $fields as $field ) {
			$key = $field['key'] ?? '';
			if ( ! isset( $data[ $key ] ) ) {
				continue;
			}
			$value = $data[ $key ];

			switch ( $field['type'] ?? '' ) {
				case 'email':
					$sanitized[ $key ] = sanitize_email( $value );
					break;
				case 'number':
				case 'range':
					$sanitized[ $key ] = sanitize_text_field( $value );
					break;
				case 'checkbox':
				case 'multi-select':
					$sanitized[ $key ] = is_array( $value ) ? array_map( 'sanitize_text_field', $value ) : sanitize_text_field( $value );
					break;
				case 'file-upload':
					$sanitized[ $key ] = $value;
					break;
				default:
					$sanitized[ $key ] = sanitize_text_field( $value );
					break;
			}
		}

		return $sanitized;
	}

}
