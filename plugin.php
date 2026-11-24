<?php

namespace MetCraft;

use MetCraft\Base\Assets_Enqueue;
use MetCraft\Base\Assets_Register;
use MetCraft\Core\Compatibility\Init as Compatibility;

defined('ABSPATH') || exit;

final class Plugin {

	private static $instance;

	private $entries;
	private $global_settings;

	public function __construct()
	{
		add_action( 'wp_head', array( $this, 'add_meta_for_search_excluded' ) );
		add_action( 'init', array ($this, 'metcraft_permalink_setup') );
		add_action( 'wp_ajax_metcraft_admin_action', ['\MetCraft\Utils\Util', 'metcraft_admin_action'] );
		add_action( 'admin_head', array( $this, 'hide_other_plugin_notices' ), 1 );

		/** Adds a global CSS class to the body tag in the editor.**/
		add_filter('admin_body_class', fn($classes) => $classes . ' metcraft-admin' );
	}

	public function version()
	{
		return METCRAFT_VERSION;
	}

	public function package_type()
	{
		return apply_filters( 'metcraft/core/package_type', 'free' );
	}

	public function plugin_url()
	{
		return trailingslashit(plugin_dir_url(__FILE__));
	}

	public function plugin_dir()
	{
		return trailingslashit(plugin_dir_path(__FILE__));
	}

	public function core_url()
	{
		return $this->plugin_url() . 'core/';
	}

	public function core_dir()
	{
		return $this->plugin_dir() . 'core/';
	}

	public function base_url()
	{
		return $this->plugin_url() . 'base/';
	}

	public function base_dir()
	{
		return $this->plugin_dir() . 'base/';
	}

	public function utils_url()
	{
		return $this->plugin_url() . 'utils/';
	}

	public function utils_dir()
	{
		return $this->plugin_dir() . 'utils/';
	}

	public function widgets_url()
	{
		return $this->plugin_url() . 'widgets/';
	}

	public function widgets_dir()
	{
		return $this->plugin_dir() . 'widgets/';
	}

	public function public_url()
	{
		return $this->plugin_url() . 'public/';
	}

	public function public_dir()
	{
		return $this->plugin_dir() . 'public/';
	}

	public function account_url(){
		return 'https://account.metcraft.com';
	}

	public function init()
	{
		// Skip onboarding when another plugin sends the user straight to form creation.
		$is_create_form_redirect = isset( $_GET['mcf_action'], $_GET['_wpnonce'] )
			&& 'create_form' === sanitize_key( wp_unslash( $_GET['mcf_action'] ) )
			&& wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'metcraft_create_form' );

		if( ! $is_create_form_redirect ){
			// Onboarding would go here; kept minimal for free version.
		}

		if (current_user_can('manage_options')) {
			add_action('admin_menu', [$this, 'admin_menu']);
		}

		add_action('init', [$this, 'metcraft_load_textdomain_on_init'], 9);

		add_action('admin_enqueue_scripts', [$this, 'js_css_admin']);
		add_action('wp_enqueue_scripts', [$this, 'js_css_public']);

		add_action('wp_ajax_metcraft_submit_form', ['\MetCraft\Utils\Util', 'metcraft_submit_form']);
		add_action('wp_ajax_nopriv_metcraft_submit_form', ['\MetCraft\Utils\Util', 'metcraft_submit_form']);

		add_action('admin_footer', [$this, 'footer_data']);

		// Elementor is optional now that MetCraft has its own standalone builder.
		$elementor_is_loaded = did_action('elementor/loaded');
		$elementor_is_compatible = $elementor_is_loaded
			&& defined('ELEMENTOR_VERSION')
			&& version_compare(ELEMENTOR_VERSION, '3.0.1', '>=');

		if ($elementor_is_loaded && !$elementor_is_compatible) {
			$this->failed_elementor_version();
		}

		if ($elementor_is_compatible) {
			add_action('elementor/editor/before_enqueue_scripts', [$this, 'edit_view_scripts']);
			add_action('elementor/editor/after_enqueue_scripts', [$this, 'metcraft_editor_script']);
			add_action('elementor/frontend/before_enqueue_scripts', [$this, 'elementor_js']);
			add_action('elementor/editor/before_enqueue_styles', [$this, 'elementor_css']);

			Controls\Base::instance()->init();
			Widgets\Manifest::instance()->init();
		}

		// settings page
		Core\Admin\Base::instance()->init();

		Core\Forms\Auto_Increment_Entry::instance();

		Core\Forms\Base::instance()->init();
		Core\Analytics\Base::instance()->init();
		Core\FormBuilder\Init::instance()->init();
		$this->entries = Core\Entries\Base::instance();
	}

	function metcraft_editor_script(){
		Assets_Enqueue::get_script('metcraft-editor-panel-script');
	}

	function js_css_public()
	{
		$this->global_settings = \MetCraft\Core\Admin\Base::instance()->get_settings_option();
		$is_form_cpt = ('metcraft-form' === get_post_type());

		Assets_Register::get_style('metcraft-ui');
		Assets_Register::get_style('metcraft-style');
		Assets_Register::get_style('metcraft-icon');

		Assets_Register::get_style('text-editor-style');

		Assets_Register::get_script('htm');

		Assets_Register::get_script('metcraft-app');
		wp_set_script_translations( 'metcraft-app', 'metcraft' );
		Assets_Enqueue::get_script('mf-widget-frontend');

		wp_localize_script('metcraft-app', 'mcf', [
			'postType' => get_post_type(),
			'minMsg1' => __("Minimum length should be ","metcraft"),
			'Msg2' => __(" character long.","metcraft"),
			'maxMsg1' => __("Maximum length should be ","metcraft"),
			'maxNum' => __("Maximum number should be ","metcraft"),
			'minNum' => __("Minimum number should be ","metcraft"),
		]);

		// Recaptcha Support Script.
		Assets_Register::get_script('recaptcha-support');

		// recaptcha-v2
		wp_register_script('recaptcha-v2', 'https://google.com/recaptcha/api.js?render=explicit', [], null, true);

		if (isset($this->global_settings['mcf_recaptcha_version']) && ($this->global_settings['mcf_recaptcha_version'] == 'recaptcha-v3') && isset($this->global_settings['mcf_recaptcha_site_key_v3']) && ($this->global_settings['mcf_recaptcha_site_key_v3'] != '')) {
			wp_register_script('recaptcha-v3', 'https://www.google.com/recaptcha/api.js?render=' . $this->global_settings['mcf_recaptcha_site_key_v3'], [], $this->version(), false);
		}

		// for date, time
		Assets_Register::remove_script('flatpickr');
		Assets_Register::get_style('flatpickr');
		Assets_Enqueue::get_style('cute-alert');

		if($is_form_cpt){
			Assets_Enqueue::get_style('metcraft-ui');
			Assets_Enqueue::get_style('metcraft-style');
			Assets_Enqueue::get_script('htm');
			Assets_Enqueue::get_script('metcraft-app');
			Assets_Enqueue::get_script('mf-widget-frontend');
		}

		do_action('metcraft/onload/enqueue_scripts');
	}

	public function edit_view_scripts()
	{
		Assets_Enqueue::get_style('metcraft-ui');
		Assets_Enqueue::get_style('metcraft-icon');
		Assets_Enqueue::get_style('metcraft-admin-style');
		Assets_Enqueue::get_script('metcraft-ui');
		Assets_Enqueue::get_script('metcraft-admin-script');
		wp_set_script_translations( 'metcraft-admin-script', 'metcraft' );

		wp_add_inline_script('metcraft-admin-script', "
            var metcraft_api = {
                resturl: '" . get_rest_url() . "'
            }
        ");
	}

	public function elementor_js()
	{
	}

	public function elementor_css()
	{
		if ('metcraft-form' == get_post_type()) {
			Assets_Enqueue::get_style('metcraft-category-top');
		}
	}

	/**
	 * @function - {cleanoThemeConflict}
	 * @description - this function is used to remove conflict of bootstrap between metcraft & theme.
	 */
	function theme_conflict() {

		$screen = get_current_screen();
		if(in_array($screen->id, ['edit-metcraft-form', 'metcraft_page_mt-form-settings', 'metcraft-entry', 'metcraft_page_metcraft-menu-settings'])){
			wp_dequeue_script( 'bootstrap' );
		}
	}

	function js_css_admin()
	{
		Assets_Enqueue::get_style('mcf-wp-dashboard');

		$screen = get_current_screen();

		if (in_array($screen->id, ['edit-metcraft-form', 'metcraft_page_mt-form-settings', 'metcraft-entry', 'metcraft_page_metcraft-menu-settings'])) {
			Assets_Enqueue::get_style('metcraft-admin-fonts');
			Assets_Enqueue::get_style('metcraft-ui');
			Assets_Enqueue::get_style('metcraft-admin-style');
			Assets_Enqueue::get_script('metcraft-ui');
			Assets_Enqueue::get_script('metcraft-admin-script');
			wp_set_script_translations( 'metcraft-admin-script', 'metcraft' );
			wp_localize_script('metcraft-admin-script', 'metcraft_api', ['resturl' => get_rest_url(), 'admin_url' => get_admin_url()]);
		}

		if (in_array($screen->id, ['edit-metcraft-entry', 'metcraft-entry', 'edit-metcraft-form'])) {
			Assets_Enqueue::get_style('admin-form-list-style');
		}

		if ($screen->id == 'edit-metcraft-entry' || $screen->id == 'metcraft-entry') {
			Assets_Enqueue::get_style('metcraft-ui');
			Assets_Enqueue::get_style('metcraft-admin-entries-view-css');
			Assets_Enqueue::get_script('metcraft-entry-script');
			wp_localize_script('metcraft-entry-script', 'metcraft_entry_i18n', [
				'bulk_actions'  => esc_html__('Bulk actions', 'metcraft'),
				'move_to_trash' => esc_html__('Move to Trash', 'metcraft'),
				'apply'         => esc_html__('Apply', 'metcraft'),
				'filter'        => esc_html__('Filter', 'metcraft'),
				'search_item'   => esc_html__('Search Item', 'metcraft'),
				'update'        => esc_html__('Update', 'metcraft'),
			]);
		}

		 if ($screen->id == 'edit-metcraft-form') {
			Assets_Enqueue::get_style('metcraft-admin-entries-view-css');
			Assets_Enqueue::get_script('metcraft-form-script');
			wp_localize_script('metcraft-form-script', 'metcraft_form_i18n', [
				'bulk_actions'  => esc_html__('Bulk actions', 'metcraft'),
				'move_to_trash' => esc_html__('Move to Trash', 'metcraft'),
				'apply'         => esc_html__('Apply', 'metcraft'),
				'search_forms'  => esc_html__('Search Forms', 'metcraft'),
			]);
		}
	}

	/**
	 * Excluding Metcraft form from search engine.
	 */
	public function add_meta_for_search_excluded() {

		if ( in_array(get_post_type(), ['metcraft-form']) ) {
			echo '<meta name="robots" content="noindex,nofollow" />', "\n";
		}
	}

	public function footer_data()
	{

		$screen = get_current_screen();

		if ($screen->id == 'edit-metcraft-entry') {
			$args = [
				'post_type'   => 'metcraft-form',
				'post_status' => 'publish',
				'numberposts' => -1,
			];

			$forms = get_posts($args);
			$get_form_id = isset($_GET['form_id']) ? sanitize_key($_GET['form_id']) : '';
?>
			<div id='metcraft-formlist' style='display:none;'><select name='mcf_form_id' id='metcraft-form_id'>
				<option value='all' <?php echo esc_attr(((($get_form_id == 'all') || ($get_form_id == '')) ? 'selected=selected' : '')); ?>><?php echo esc_html__('All', 'metcraft'); ?></option>
				<?php

				foreach ($forms as $form) {
					$form_list[$form->ID] = $form->post_title;
					?>
					<option value="<?php echo esc_attr($form->ID); ?>" <?php echo esc_attr(($get_form_id == $form->ID) ? 'selected=selected' : '')); ?>><?php echo esc_html($form->post_title); ?></option>
        <?php
				}
			echo "</select></div>";
		}
	}

	function admin_menu()
	{
		add_menu_page(
			esc_html__( 'MetCraft', 'metcraft' ),
			esc_html__( 'MetCraft', 'metcraft' ),
			'read',
			'metcraft-menu',
			[ $this, 'render_dashboard' ],
			$this->plugin_url() . 'build/assets/images/admin/icon-menu.png',
			5
		);
	}

	public function render_dashboard()
	{
		require METCRAFT_FREE_PATH . 'build/admin/views/dashboard.php';
	}

	public function failed_elementor_version()
	{

		$btn['text'] = esc_html__('Update Elementor', 'metcraft');
		$btn['url'] = wp_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&plugin=elementor/elementor.php' ), 'upgrade-plugin_elementor/elementor.php' );
		$btn['class'] = 'button-primary';

		$message = sprintf(esc_html__('MetCraft requires Elementor version %1$s+, which is currently NOT RUNNING.', 'metcraft'), '3.0.1');
		\MetCraft\Libs\Notice::instance('metcraft', 'unsupported-elementor-version')
			->set_dismiss('global', (3600 * 24 * 15))
			->set_message($message)
			->set_button($btn)
			->call();
	}

	public static function instance()
	{
		if (!self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function metcraft_permalink_setup(){

		Utils\Util::permalink_setup();
	}

	/**
	 * Hide other plugins/themes admin notices on MetCraft pages
	 * Only show MetCraft's own notices
	 */
	public function hide_other_plugin_notices() {
		$screen = get_current_screen();

		if (!$screen) {
			return;
		}

		// Check if current page is a MetCraft page by screen ID
		$is_metcraft_page = (strpos($screen->id, 'metcraft') !== false);

		// Also check for post_type parameter
		if (!$is_metcraft_page && isset($_GET['post_type'])) {
			$post_type = sanitize_text_field($_GET['post_type']);
			$is_metcraft_page = (strpos($post_type, 'metcraft') !== false);
		}

		// Check if current page is a MetCraft page
		if ($is_metcraft_page) {
			global $wp_filter;

			// Store MetCraft notices before removing all
			$metcraft_notices = [];

			if (isset($wp_filter['admin_notices'])) {
				foreach ($wp_filter['admin_notices']->callbacks as $priority => $callbacks) {
					foreach ($callbacks as $key => $callback) {
						// Keep only MetCraft notices
						if (is_array($callback['function'])) {
							$class = is_object($callback['function'][0]) ? get_class($callback['function'][0]) : $callback['function'][0];
							if (strpos($class, 'MetCraft') !== false) {
								$metcraft_notices[$priority][$key] = $callback;
							}
						} elseif (is_string($callback['function']) && (strpos($callback['function'], 'metcraft') !== false)) {
							$metcraft_notices[$priority][$key] = $callback;
						}
					}
				}
			}

			// Remove all admin notices
			remove_all_actions('admin_notices');
			remove_all_actions('all_admin_notices');

			// Re-add only MetCraft notices
			if (!empty($metcraft_notices)) {
				foreach ($metcraft_notices as $priority => $callbacks) {
					foreach ($callbacks as $callback) {
						add_action('admin_notices', $callback['function'], $priority, $callback['accepted_args']);
					}
				}
			}
		}
	}

	public function flush_rewrites()
	{
		$form_cpt = new Core\Forms\Cpt();
		$form_cpt->flush_rewrites();
	}

	public function metcraft_load_textdomain_on_init(){
		load_plugin_textdomain( 'metcraft', false, plugin_dir_path( METCRAFT_PLUGIN_FILE ) . 'languages/' );
	}

}
