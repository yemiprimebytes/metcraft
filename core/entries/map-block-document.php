<?php

namespace MetCraft\Core\Entries;

defined( 'ABSPATH' ) || exit;

/**
 * Map block document for entries (placeholder for block editor support).
 */
class Map_Block_Document {

	public static function is_block_form( $post_id ) {
		return false;
	}

}
