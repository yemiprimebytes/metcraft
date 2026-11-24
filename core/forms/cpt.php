<?php

namespace MetCraft\Core\Forms;

defined( 'ABSPATH' ) || exit;

/**
 * Custom post type handling for MetCraft forms.
 */
class Cpt {

	public function flush_rewrites() {
		flush_rewrite_rules();
	}

}
