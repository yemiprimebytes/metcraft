<?php

namespace MetCraft\Core\Compatibility;

defined( 'ABSPATH' ) || exit;

/**
 * Compatibility initialization for MetCraft.
 */
class Init {

	private static $instance;

	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function init() {
		// Compatibility checks and fixes
		add_action( 'plugins_loaded', [ $this, 'check_elementor' ], 10 );
	}

	public function check_elementor() {
		// Elementor is optional for MetCraft
	}

}
