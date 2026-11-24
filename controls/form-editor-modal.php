<?php

namespace MetCraft\Controls;

defined( 'ABSPATH' ) || exit;

/**
 * Form editor modal data for the Elementor integration.
 */
class Form_Editor_Modal {

	public static function get_modal_data() {
		return [
			'title'       => __( 'Edit Form', 'metcraft' ),
			'builder_url' => admin_url( 'admin.php?page=metcraft-builder' ),
		];
	}

}
