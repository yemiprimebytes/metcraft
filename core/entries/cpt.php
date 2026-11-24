<?php

namespace MetCraft\Core\Entries;

defined( 'ABSPATH' ) || exit;

/**
 * Entry CPT helpers.
 */
class Cpt {

	public function flush_rewrites() {
		flush_rewrite_rules();
	}

}
