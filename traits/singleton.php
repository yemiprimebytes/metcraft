<?php

namespace MetCraft\Traits;

defined( 'ABSPATH' ) || exit;

/**
 * Singleton trait for MetCraft classes.
 */
trait Singleton {

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

}
