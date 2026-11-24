<?php

namespace MetCraft\Core\Entries;

defined( 'ABSPATH' ) || exit;

/**
 * Action handlers for entries.
 */
class Action {

	public static function init() {
		add_action( 'metcraft/entry/created', [ self::class, 'on_entry_created' ], 10, 2 );
	}

	public static function on_entry_created( $entry_id, $form_id ) {
		// Extensible action after entry creation
	}

}
