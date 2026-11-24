<?php

namespace MetCraft\Base;

defined( 'ABSPATH' ) || exit;

/**
 * Common utilities for the base layer.
 */
class Common {

	public static function get_plugin_url() {
		return trailingslashit( plugin_dir_url( METCRAFT_PLUGIN_FILE ) );
	}

	public static function get_plugin_dir() {
		return trailingslashit( plugin_dir_path( METCRAFT_PLUGIN_FILE ) );
	}

}
