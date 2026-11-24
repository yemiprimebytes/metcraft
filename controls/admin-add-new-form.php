<?php

namespace MetCraft\Controls;

defined( 'ABSPATH' ) || exit;

/**
 * Admin add-new-form control / modal data.
 */
class Admin_Add_New_Form {

	public static function get_template_data() {
		return [
			'templates' => self::get_templates(),
		];
	}

	public static function get_templates() {
		return [
			'contact'  => [
				'title' => __( 'Contact Form', 'metcraft' ),
				'fields'=> [],
			],
			'booking'  => [
				'title' => __( 'Booking Form', 'metcraft' ),
				'fields'=> [],
			],
			'feedback' => [
				'title' => __( 'Feedback Form', 'metcraft' ),
				'fields'=> [],
			],
			'calculation'=> [
				'title' => __( 'Calculation Form', 'metcraft' ),
				'fields'=> [],
			],
		];
	}

}
