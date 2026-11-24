<?php

namespace MetCraft\Controls;

defined( 'ABSPATH' ) || exit;

use \Elementor\Controls_Manager;

/**
 * Form picker control for selecting a form in the Elementor widget.
 */
class Form_Picker {

	public static function register( $widget ) {
		$widget->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Form', 'metcraft' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$widget->add_control(
			'mcf_form_id',
			[
				'label'   => esc_html__( 'Select Form: ', 'metcraft' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => self::get_form_options(),
			]
		);

		$widget->end_controls_section();
	}

	public static function get_form_options() {
		$forms = get_posts( [
			'posts_per_page' => -1,
			'post_type'      => 'metcraft-form',
			'post_status'    => 'publish',
		] );

		$options = [];
		foreach ( $forms as $form ) {
			$options[ $form->ID ] = $form->post_title;
		}

		return $options;
	}

}
