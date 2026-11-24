<?php

namespace MetCraft\Core\Entries;

defined( 'ABSPATH' ) || exit;

/**
 * Shortcode-based form rendering for entries/front-end.
 */
class Metcraft_Shortcode {

	public static function render( $form_id, $widget_id = '' ) {
		$form_id = absint( $form_id );
		if ( ! $form_id || 'metcraft-form' !== get_post_type( $form_id ) ) {
			return esc_html__( 'No form found.', 'metcraft' );
		}

		$form = \MetCraft\Core\Forms\Base::instance()->get_form( $form_id );
		if ( ! $form ) {
			return esc_html__( 'No form found.', 'metcraft' );
		}

		ob_start();
		\MetCraft\Utils\Util::metcraft_content_renderer( [
			'id'      => $form_id,
			'title'   => $form['title'],
			'fields'  => $form['fields'],
			'settings'=> $form['settings'],
			'widget_id'=> $widget_id,
		] );
		return ob_get_clean();
	}

}
