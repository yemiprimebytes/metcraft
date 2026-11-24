<?php

namespace MetCraft\Base;

defined( 'ABSPATH' ) || exit;

/**
 * Base asset definitions (styles and scripts) for MetCraft.
 */
class Assets_Base {

	public static function get_assets() {
		$plugin_url = METCRAFT_FREE_URL;

		return [
			'metcraft-ui' => [
				'style' => true,
				'src'   => $plugin_url . 'build/styles/metcraft-ui.css',
				'ver'   => METCRAFT_VERSION,
			],
			'metcraft-style' => [
				'style' => true,
				'src'   => $plugin_url . 'build/styles/metcraft-style.css',
				'ver'   => METCRAFT_VERSION,
			],
			'metcraft-icon' => [
				'style' => true,
				'src'   => $plugin_url . 'build/assets/fonts/metcraft-icons.css',
				'ver'   => METCRAFT_VERSION,
			],
			'metcraft-admin-style' => [
				'style' => true,
				'src'   => $plugin_url . 'build/styles/metcraft-admin.css',
				'ver'   => METCRAFT_VERSION,
			],
			'metcraft-category-top' => [
				'style' => true,
				'src'   => $plugin_url . 'build/styles/category-top.css',
				'ver'   => METCRAFT_VERSION,
			],
			'text-editor-style' => [
				'style' => true,
				'src'   => $plugin_url . 'build/assets/css/text-editor.css',
				'ver'   => METCRAFT_VERSION,
			],
			'flatpickr' => [
				'style' => true,
				'src'   => $plugin_url . 'assets/libs/flatpickr/flatpickr.min.css',
				'ver'   => METCRAFT_VERSION,
			],
			'cute-alert' => [
				'style' => true,
				'src'   => $plugin_url . 'assets/libs/cute-alert/cute-alert.min.css',
				'ver'   => METCRAFT_VERSION,
			],
			'mcf-wp-dashboard' => [
				'style' => true,
				'src'   => $plugin_url . 'build/admin/metcraft-ui/css/mcf-wp-dashboard.css',
				'ver'   => METCRAFT_VERSION,
			],
			'metcraft-admin-fonts' => [
				'style' => true,
				'src'   => 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap',
				'ver'   => METCRAFT_VERSION,
			],
			'admin-form-list-style' => [
				'style' => true,
				'src'   => $plugin_url . 'build/admin/entries-list/css/admin-form-list.css',
				'ver'   => METCRAFT_VERSION,
			],
			'metcraft-admin-entries-view-css' => [
				'style' => true,
				'src'   => $plugin_url . 'build/admin/entry-details/css/metcraft-admin-entries-view.css',
				'ver'   => METCRAFT_VERSION,
			],
			'htm' => [
				'script' => true,
				'src'   => $plugin_url . 'assets/libs/htm/htm.min.js',
				'ver'   => METCRAFT_VERSION,
			],
			'metcraft-app' => [
				'script' => true,
				'src'   => $plugin_url . 'build/form-builder/index.js',
				'deps'  => [ 'htm' ],
				'ver'   => METCRAFT_VERSION,
				'localize' => [
					'name' => 'mcf_app',
					'data' => [
						'restUrl' => esc_url_raw( rest_url() ),
						'nonce'   => wp_create_nonce( 'wp_rest' ),
					],
				],
			],
			'mf-widget-frontend' => [
				'script' => true,
				'src'   => $plugin_url . 'build/frontend/widget-bootstrap/widget-bootstrap.js',
				'ver'   => METCRAFT_VERSION,
				'in_footer' => true,
			],
			'metcraft-editor-panel-script' => [
				'script' => true,
				'src'   => $plugin_url . 'build/admin/editor-panel/js/metcraft-editor-panel.js',
				'deps'  => [ 'metcraft-app' ],
				'ver'   => METCRAFT_VERSION,
				'in_footer' => true,
			],
			'metcraft-admin-script' => [
				'script' => true,
				'src'   => $plugin_url . 'build/admin/js/metcraft-admin.js',
				'ver'   => METCRAFT_VERSION,
				'in_footer' => true,
			],
			'metcraft-entry-script' => [
				'script' => true,
				'src'   => $plugin_url . 'build/admin/entries-list/js/metcraft-entry.js',
				'ver'   => METCRAFT_VERSION,
				'in_footer' => true,
			],
			'metcraft-form-script' => [
				'script' => true,
				'src'   => $plugin_url . 'build/admin/form-list/js/metcraft-form.js',
				'ver'   => METCRAFT_VERSION,
				'in_footer' => true,
			],
			'recaptcha-support' => [
				'script' => true,
				'src'   => $plugin_url . 'build/frontend/recaptcha-support/recaptcha-support.js',
				'ver'   => METCRAFT_VERSION,
				'in_footer' => true,
			],
			'select2' => [
				'script' => true,
				'src'   => $plugin_url . 'assets/libs/select2/js/select2.full.min.js',
				'ver'   => METCRAFT_VERSION,
				'deps'  => [ 'jquery' ],
				'in_footer' => true,
			],
			'asRange' => [
				'script' => true,
				'src'   => $plugin_url . 'assets/libs/asRange/js/asRange.min.js',
				'ver'   => METCRAFT_VERSION,
				'in_footer' => true,
			],
		];
	}

}
