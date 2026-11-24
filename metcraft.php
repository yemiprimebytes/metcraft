<?php
/**
 * Plugin Name: MetCraft
 * Plugin URI: https://metcraft.com/plugin/metcraft/
 * Description: Most flexible and design friendly form builder for Elementor
 * Version: 1.0.0
 * Author: MetCraft
 * Author URI: https://metcraft.com
 * Text Domain: metcraft
 * Domain Path: /languages
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.txt
 */

defined( 'ABSPATH' ) || exit;

define( 'METCRAFT_PLUGIN_FILE', __FILE__ );
define( 'METCRAFT_VERSION', get_file_data( METCRAFT_PLUGIN_FILE, [ 'version' => 'Version' ] )['version'] );
define( 'METCRAFT_FREE_PATH', plugin_dir_path( METCRAFT_PLUGIN_FILE ));
define( 'METCRAFT_FREE_URL', plugin_dir_url( METCRAFT_PLUGIN_FILE ));

// All features are included in this single plugin.
define( 'METCRAFT_ALL_FEATURES', 1 );

require plugin_dir_path( METCRAFT_PLUGIN_FILE ) . 'autoloader.php';
require plugin_dir_path( METCRAFT_PLUGIN_FILE ) . 'plugin.php';

register_activation_hook( METCRAFT_PLUGIN_FILE, [ MetCraft\Plugin::instance(), 'flush_rewrites' ] );

add_action( 'plugins_loaded', function(){
    do_action('metcraft/before_load');
    MetCraft\Plugin::instance()->init();
    do_action('metcraft/after_load');
}, 111);
