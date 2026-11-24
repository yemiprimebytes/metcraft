<?php

namespace MetCraft\Core\Forms;

defined( 'ABSPATH' ) || exit;

/**
 * Builder-related functionality for MetCraft forms.
 */
class Builder {

	public static function get_builder_data( $form_id ) {
		$form_id = absint( $form_id );
		$form    = get_post( $form_id );

		if ( ! $form || 'metcraft-form' !== $form->post_type ) {
			return [];
		}

		$form_data = get_post_meta( $form_id, '_metcraft_form_data', true );
		$settings  = get_post_meta( $form_id, '_metcraft_form_settings', true );

		return [
			'id'      => $form_id,
			'title'   => $form->post_title,
			'fields'  => isset( $form_data['fields'] ) ? $form_data['fields'] : [],
			'settings'=> is_array( $settings ) ? $settings : [],
		];
	}

	public static function save_builder_data( $form_id, $data ) {
		$form_id = absint( $form_id );
		update_post_meta( $form_id, '_metcraft_form_data', $data );
		return true;
	}

}
