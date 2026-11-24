<?php

namespace MetCraft\Core\Entries;

defined( 'ABSPATH' ) || exit;

/**
 * Meta data helpers for entries.
 */
class Meta_Data {

	public static function get_entry_meta( $entry_id, $key = '' ) {
		if ( $key ) {
			return get_post_meta( $entry_id, $key, true );
		}
		return get_post_meta( $entry_id );
	}

	public static function update_entry_meta( $entry_id, $key, $value ) {
		return update_post_meta( $entry_id, $key, $value );
	}

}
