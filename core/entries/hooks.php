<?php

namespace MetCraft\Core\Entries;

defined( 'ABSPATH' ) || exit;

/**
 * Entry hooks for MetCraft.
 */
class Hooks {

	public static function init() {
		add_action( 'metcraft_form_submitted', [ self::class, 'on_submitted' ], 10, 3 );
	}

	public static function on_submitted( $form_id, $entry_data, $files ) {
		do_action( 'metcraft/after_entry_created', $form_id, $entry_data, $files );
	}

}
