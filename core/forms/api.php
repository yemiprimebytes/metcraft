<?php

namespace MetCraft\Core\Forms;

defined( 'ABSPATH' ) || exit;

/**
 * API endpoints for MetCraft forms.
 */
class Api {

	public static function init() {
		add_action( 'rest_api_init', [ self::class, 'register_routes' ] );
	}

	public static function register_routes() {
		register_rest_route( 'metcraft/v1', '/forms', [
			'methods'             => 'GET',
			'callback'            => [ self::class, 'get_forms' ],
			'permission_callback' => '__return_true',
		] );

		register_rest_route( 'metcraft/v1', '/forms/(?P<id>\d+)', [
			'methods'             => 'GET',
			'callback'            => [ self::class, 'get_form' ],
			'permission_callback' => '__return_true',
		] );
	}

	public static function get_forms() {
		$forms = get_posts( [
			'posts_per_page' => -1,
			'post_type'      => 'metcraft-form',
			'post_status'    => 'publish',
		] );

		$list = [];
		foreach ( $forms as $form ) {
			$list[] = [
				'id'    => $form->ID,
				'title' => $form->post_title,
			];
		}

		return rest_ensure_response( $list );
	}

	public static function get_form( $request ) {
		$id = absint( $request->get_param( 'id' ) );
		$form = get_post( $id );

		if ( ! $form || 'metcraft-form' !== $form->post_type ) {
			return rest_ensure_response( [ 'error' => 'Form not found' ], 404 );
		}

		$data = get_post_meta( $id, '_metcraft_form_data', true );
		if ( ! is_array( $data ) ) {
			$data = [];
		}

		return rest_ensure_response( [
			'id'      => $id,
			'title'   => $form->post_title,
			'fields'  => isset( $data['fields'] ) ? $data['fields'] : [],
		] );
	}

}
