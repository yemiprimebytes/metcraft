<?php

namespace MetCraft\Core\Entries;

defined( 'ABSPATH' ) || exit;

/**
 * File data validation for entry file uploads.
 */
class File_Data_Validation {

	public static function validate_file( $file, $allowed_types = [], $max_size = 0 ) {
		$errors = [];

		if ( $file['error'] !== UPLOAD_ERR_OK ) {
			$errors[] = __( 'File upload error.', 'metcraft' );
			return $errors;
		}

		if ( ! empty( $allowed_types ) ) {
			$ext = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );
			if ( ! in_array( $ext, $allowed_types, true ) ) {
				$errors[] = sprintf( __( 'File type "%s" is not allowed.', 'metcraft' ), $ext );
			}
		}

		if ( $max_size > 0 && $file['size'] > $max_size ) {
			$errors[] = __( 'File exceeds the maximum allowed size.', 'metcraft' );
		}

		return $errors;
	}

}
