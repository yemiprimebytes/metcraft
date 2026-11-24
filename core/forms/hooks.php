<?php

namespace MetCraft\Core\Forms;

defined( 'ABSPATH' ) || exit;

/**
 * Hooks for MetCraft forms.
 */
class Hooks {

	public static function init() {
		add_action( 'metcraft_form_submitted', [ self::class, 'on_form_submitted' ], 10, 2 );
	}

	public static function on_form_submitted( $form_id, $entry_data ) {
		// Extensible hook for form submission handling
		do_action( 'metcraft/after_form_submission', $form_id, $entry_data );
	}

}
