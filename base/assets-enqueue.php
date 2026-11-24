<?php

namespace MetCraft\Base;

defined( 'ABSPATH' ) || exit;

/**
 * Enqueues assets for MetCraft.
 */
class Assets_Enqueue {

	public static function get_style( $handle ) {
		wp_enqueue_style( $handle );
	}

	public static function get_script( $handle ) {
		wp_enqueue_script( $handle );
	}

}
