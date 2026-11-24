<?php

namespace MetCraft\Controls;

defined( 'ABSPATH' ) || exit;

/**
 * Base control class for MetCraft form builder controls.
 */
class Base {

	private static $instance;

	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function init() {
		add_action( 'elementor/elements/categories_registered', [ $this, 'add_metcraft_widget_categories' ] );
		add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );
	}

	public function add_metcraft_widget_categories( $elements_manager ) {
		$elements_manager->add_category(
			'metcraft',
			[
				'title' => esc_html__( 'MetCraft', 'metcraft' ),
				'icon'  => 'fa fa-plug',
			]
		);
	}

	public function register_widgets() {
		// Widgets are registered via the manifest
	}

}
