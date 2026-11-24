<?php

namespace MetCraft\Core\Integrations\Onboard;

defined( 'ABSPATH' ) || exit;

class Onboard {

	private static $instance;

	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function init() {
		// Onboarding placeholder for free version
	}

}
