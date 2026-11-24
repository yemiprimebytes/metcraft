<?php

namespace MetCraft\Core\Forms;

defined( 'ABSPATH' ) || exit;

/**
 * Auto-increment entry number for form submissions.
 */
class Auto_Increment_Entry {

	private static $instance;

	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function __construct() {
		add_action( 'init', [ $this, 'init' ] );
	}

	public function init() {
		// Hook into entry creation to set auto-increment number
		add_action( 'metcraft/entry/created', [ $this, 'assign_entry_number' ], 10, 2 );
	}

	public function assign_entry_number( $entry_id, $form_id ) {
		$key = '_metcraft_entry_number_' . $form_id;
		$number = (int) get_option( $key, 0 ) + 1;
		update_option( $key, $number );
		update_post_meta( $entry_id, '_metcraft_entry_number', $number );
		return $number;
	}

	public function get_next_number( $form_id ) {
		$key = '_metcraft_entry_number_' . $form_id;
		return (int) get_option( $key, 0 ) + 1;
	}

}
