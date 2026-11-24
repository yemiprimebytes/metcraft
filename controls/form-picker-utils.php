<?php

namespace MetCraft\Controls;

defined( 'ABSPATH' ) || exit;

/**
 * Form picker utility - parses a form ID and returns renderable form data.
 */
class Form_Picker_Utils {

	/**
	 * Parse a form ID and return the form data for rendering.
	 *
	 * @param int    $form_id Form post ID.
	 * @param string $widget_id Unique widget ID.
	 * @return array
	 */
	public static function parse( $form_id, $widget_id = '' ) {
		$form_id = absint( $form_id );

		if ( ! $form_id || 'metcraft-form' !== get_post_type( $form_id ) ) {
			return [
				'id'    => 0,
				'title' => '',
				'fields'=> [],
				'settings'=> [],
			];
		}

		$form_data = get_post_meta( $form_id, '_metcraft_form_data', true );
		if ( ! is_array( $form_data ) ) {
			$form_data = [];
		}

		$settings = get_post_meta( $form_id, '_metcraft_form_settings', true );
		if ( ! is_array( $settings ) ) {
			$settings = [];
		}

		return [
			'id'      => $form_id,
			'title'   => get_the_title( $form_id ),
			'fields'  => isset( $form_data['fields'] ) ? $form_data['fields'] : [],
			'settings'=> $settings,
			'widget_id'=> $widget_id,
		];
	}

}
