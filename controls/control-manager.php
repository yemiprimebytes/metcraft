<?php

namespace MetCraft\Controls;

defined( 'ABSPATH' ) || exit;

use \Elementor\Controls_Manager;

/**
 * Control manager for MetCraft custom controls.
 */
class Control_Manager {

	private static $instance;

	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function init() {
		// Custom controls registration
	}

	const TAB_CONTENT = Controls_Manager::TAB_CONTENT;
	const TAB_STYLE   = Controls_Manager::TAB_STYLE;
	const TAB_ADVANCED = Controls_Manager::TAB_ADVANCED;

}
