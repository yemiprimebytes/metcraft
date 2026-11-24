<?php

namespace MetCraft\Core\FormBuilder;

defined( 'ABSPATH' ) || exit;

/**
 * Form builder initialization for MetCraft.
 */
class Init {

	private static $instance;

	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function init() {
		add_action( 'admin_menu', [ $this, 'add_builder_menu' ] );
		add_action( 'wp_ajax_metcraft_get_form_templates', [ $this, 'ajax_get_templates' ] );
		add_action( 'wp_ajax_nopriv_metcraft_get_form_templates', [ $this, 'ajax_get_templates' ] );
	}

	public function add_builder_menu() {
		add_submenu_page(
			'metcraft-menu',
			__( 'Form Builder', 'metcraft' ),
			__( 'Form Builder', 'metcraft' ),
			'manage_options',
			'metcraft-builder',
			[ $this, 'render_builder_page' ]
		);
	}

	public function render_builder_page() {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'MetCraft Form Builder', 'metcraft' ); ?></h1>
			<div id="metcraft-builder-app">
				<p><?php esc_html_e( 'Use the Elementor widget to build and edit your forms, or use shortcodes to embed them anywhere.', 'metcraft' ); ?></p>
				<a href="<?php echo esc_url( admin_url( 'post.php?post=0&action=edit&post_type=metcraft-form' ) ); ?>" class="button button-primary"><?php esc_html_e( 'Create New Form', 'metcraft' ); ?></a>
			</div>
		</div>
		<?php
	}

	public function ajax_get_templates() {
		check_ajax_referer( 'metcraft_templates_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'Unauthorized', 'metcraft' ) );
		}

		$templates = \MetCraft\Controls\Admin_Add_New_Form::get_templates();
		wp_send_json_success( $templates );
	}

}
