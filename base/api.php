<?php

namespace MetCraft\Base;

defined( 'ABSPATH' ) || exit;

/**
 * API-related helpers for the base layer.
 */
class Api {

	public static function rest_url() {
		return esc_url_raw( rest_url() );
	}

}
