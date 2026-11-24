<?php

namespace MetCraft\Core\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Admin settings base for MetCraft.
 */
class Base {

	private static $instance;
	private $settings;

	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function init() {
		add_action( 'admin_init', [ $this, 'register_settings' ] );
		add_action( 'admin_menu', [ $this, 'add_settings_page' ] );
	}

	public function register_settings() {
		register_setting( 'metcraft_settings_group', 'metcraft_settings' );

		add_settings_section(
			'metcraft_general_section',
			__( 'General Settings', 'metcraft' ),
			[ $this, 'render_general_section' ],
			'metcraft-settings'
		);

		add_settings_field(
			'mcf_recaptcha_site_key',
			__( 'reCAPTCHA Site Key', 'metcraft' ),
			[ $this, 'render_recaptcha_site_key' ],
			'metcraft-settings',
			'metcraft_general_section'
		);

		add_settings_field(
			'mcf_recaptcha_secret_key',
			__( 'reCAPTCHA Secret Key', 'metcraft' ),
			[ $this, 'render_recaptcha_secret_key' ],
			'metcraft-settings',
			'metcraft_general_section'
		);

		add_settings_field(
			'mcf_recaptcha_version',
			__( 'reCAPTCHA Version', 'metcraft' ),
			[ $this, 'render_recaptcha_version' ],
			'metcraft-settings',
			'metcraft_general_section'
		);
	}

	public function add_settings_page() {
		add_submenu_page(
			'metcraft-menu',
			__( 'Settings', 'metcraft' ),
			__( 'Settings', 'metcraft' ),
			'manage_options',
			'metcraft-menu-settings',
			[ $this, 'render_settings_page' ]
		);
	}

	public function render_general_section() {
		echo '<p>' . esc_html__( 'Configure global settings for MetCraft forms.', 'metcraft' ) . '</p>';
	}

	public function render_recaptcha_site_key() {
		$settings = $this->get_settings_option();
		$value    = isset( $settings['mcf_recaptcha_site_key'] ) ? $settings['mcf_recaptcha_site_key'] : '';
		echo '<input type="text" name="metcraft_settings[mcf_recaptcha_site_key]" value="' . esc_attr( $value ) . '" class="regular-text" />';
		echo '<p class="description">' . esc_html__( 'Enter your Google reCAPTCHA site key.', 'metcraft' ) . '</p>';
	}

	public function render_recaptcha_secret_key() {
		$settings = $this->get_settings_option();
		$value    = isset( $settings['mcf_recaptcha_secret_key'] ) ? $settings['mcf_recaptcha_secret_key'] : '';
		echo '<input type="text" name="metcraft_settings[mcf_recaptcha_secret_key]" value="' . esc_attr( $value ) . '" class="regular-text" />';
		echo '<p class="description">' . esc_html__( 'Enter your Google reCAPTCHA secret key.', 'metcraft' ) . '</p>';
	}

	public function render_recaptcha_version() {
		$settings = $this->get_settings_option();
		$value    = isset( $settings['mcf_recaptcha_version'] ) ? $settings['mcf_recaptcha_version'] : 'recaptcha-v2';
		?>
		<select name="metcraft_settings[mcf_recaptcha_version]">
			<option value="recaptcha-v2" <?php selected( $value, 'recaptcha-v2' ); ?>><?php esc_html_e( 'reCAPTCHA v2', 'metcraft' ); ?></option>
			<option value="recaptcha-v3" <?php selected( $value, 'recaptcha-v3' ); ?>><?php esc_html_e( 'reCAPTCHA v3', 'metcraft' ); ?></option>
		</select>
		<?php
	}

	public function render_settings_page() {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'MetCraft Settings', 'metcraft' ); ?></h1>
			<form action="options.php" method="post">
				<?php
				settings_fields( 'metcraft_settings_group' );
				do_settings_sections( 'metcraft-settings' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	public function get_settings_option() {
		return get_option( 'metcraft_settings', [] );
	}

}
