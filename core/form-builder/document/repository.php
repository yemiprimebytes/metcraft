<?php

namespace MetCraft\Core\FormBuilder\Document;

defined( 'ABSPATH' ) || exit;

/**
 * Repository for form builder documents.
 */
class Repository {

	public static function is_builder_form( $post_id ) {
		$post_id = absint( $post_id );
		if ( ! $post_id ) {
			return false;
		}
		$is_builder = get_post_meta( $post_id, '_metcraft_is_builder_form', true );
		return (bool) $is_builder;
	}

	public static function mark_as_builder_form( $post_id ) {
		$post_id = absint( $post_id );
		update_post_meta( $post_id, '_metcraft_is_builder_form', 'yes' );
	}

}
