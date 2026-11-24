<?php

namespace MetCraft\Base;

defined( 'ABSPATH' ) || exit;

/**
 * Shortcode support for rendering MetCraft forms anywhere.
 */
class Shortcode {

	private static $instance;

	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function init() {
		add_shortcode( 'metcraft', [ $this, 'render' ] );
		add_shortcode( 'metcraft_form', [ $this, 'render' ] );
	}

	/**
	 * Render a form via shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string
	 */
	public function render( $atts ) {
		$atts = shortcode_atts( [
			'id' => '',
		], $atts, 'metcraft' );

		$form_id = absint( $atts['id'] );

		if ( ! $form_id || 'metcraft-form' !== get_post_type( $form_id ) ) {
			return esc_html__( 'No form selected.', 'metcraft' );
		}

		ob_start();
		\MetCraft\Utils\Util::metcraft_content_renderer( \MetCraft\Controls\Form_Picker_Utils::parse( $form_id, uniqid( 'mcf-' ) ) );
		return ob_get_clean();
	}

}
