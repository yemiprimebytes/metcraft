<?php

namespace MetCraft\Base;

defined( 'ABSPATH' ) || exit;

/**
 * Registers the MetCraft form custom post type.
 */
class Cpt {

	private static $instance;

	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function init() {
		add_action( 'init', [ $this, 'register_post_types' ] );
	}

	public function register_post_types() {
		// Form post type
		$form_labels = [
			'name'                  => __( 'Forms', 'metcraft' ),
			'singular_name'         => __( 'Form', 'metcraft' ),
			'add_new'               => __( 'Add New', 'metcraft' ),
			'add_new_item'          => __( 'Add New Form', 'metcraft' ),
			'edit_item'             => __( 'Edit Form', 'metcraft' ),
			'new_item'              => __( 'New Form', 'metcraft' ),
			'view_item'             => __( 'View Form', 'metcraft' ),
			'search_items'          => __( 'Search Forms', 'metcraft' ),
			'not_found'             => __( 'No forms found', 'metcraft' ),
			'not_found_in_trash'    => __( 'No forms found in Trash', 'metcraft' ),
			'parent_item_colon'     => __( 'Parent Form:', 'metcraft' ),
			'all_items'             => __( 'All Forms', 'metcraft' ),
			'menu_name'             => __( 'Forms', 'metcraft' ),
		];

		$form_args = [
			'labels'              => $form_labels,
			'public'              => true,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_in_rest'        => true,
			'rest_base'           => 'metcraft-forms',
			'has_archive'         => false,
			'show_ui'             => true,
			'show_in_menu'        => false,
			'show_in_admin_bar'   => false,
			'menu_position'       => null,
			'menu_icon'           => 'dashicons-format-chat',
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
			'hierarchical'        => false,
			'rewrite'             => [ 'slug' => 'metcraft-form', 'with_front' => false ],
			'query_var'           => 'metcraft-form',
			'supports'            => [ 'title', 'editor', 'thumbnail', 'custom-fields', 'revisions' ],
		];

		register_post_type( 'metcraft-form', $form_args );

		// Entry post type
		$entry_labels = [
			'name'                  => __( 'Entries', 'metcraft' ),
			'singular_name'         => __( 'Entry', 'metcraft' ),
			'add_new'               => __( 'Add New', 'metcraft' ),
			'add_new_item'          => __( 'Add New Entry', 'metcraft' ),
			'edit_item'             => __( 'Edit Entry', 'metcraft' ),
			'new_item'              => __( 'New Entry', 'metcraft' ),
			'view_item'             => __( 'View Entry', 'metcraft' ),
			'search_items'          => __( 'Search Entries', 'metcraft' ),
			'not_found'             => __( 'No entries found', 'metcraft' ),
			'not_found_in_trash'    => __( 'No entries found in Trash', 'metcraft' ),
			'all_items'             => __( 'All Entries', 'metcraft' ),
			'menu_name'             => __( 'Entries', 'metcraft' ),
		];

		$entry_args = [
			'labels'              => $entry_labels,
			'public'              => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_in_rest'        => true,
			'rest_base'           => 'metcraft-entries',
			'has_archive'         => false,
			'show_ui'             => true,
			'show_in_menu'        => 'metcraft-menu',
			'show_in_admin_bar'   => false,
			'menu_position'       => null,
			'menu_icon'           => 'dashicons-view',
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
			'hierarchical'        => false,
			'rewrite'             => [ 'slug' => 'metcraft-entry', 'with_front' => false ],
			'query_var'           => 'metcraft-entry',
			'supports'            => [ 'title', 'custom-fields', 'revisions' ],
		];

		register_post_type( 'metcraft-entry', $entry_args );
	}

	public function flush_rewrites() {
		$this->register_post_types();
		flush_rewrite_rules();
	}

}
