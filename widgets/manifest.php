<?php
namespace MetCraft\Widgets;
defined( 'ABSPATH' ) || exit;

Class Manifest{
	use \MetCraft\Traits\Singleton;

	public function init() {
		add_action( 'elementor/elements/categories_registered', [ $this, 'add_metcraft_widget_categories' ]);
		add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );
	}

	/**
	 * Get input widgets
	 */
	public function get_input_widgets(){
		$widget_list = [
			'mcf-text',
			'mcf-email',
			'mcf-number',
			'mcf-telephone',
			'mcf-date',
			'mcf-time',
			'mcf-select',
			'mcf-multi-select',
			'mcf-textarea',
			'mcf-checkbox',
			'mcf-radio',
			'mcf-switch',
			'mcf-range',
			'mcf-url',
			'mcf-password',
			'mcf-gdpr-consent',
			'mcf-recaptcha',
			'mcf-simple-captcha',
			'mcf-rating',
			'mcf-file-upload',
			'mcf-summary',
			'mcf-simple-message',
		];

		return apply_filters( 'metcraft/onload/input_widgets', $widget_list );
	}

	public function includes() {

		require_once plugin_dir_path(__FILE__) . 'form.php';
		require_once plugin_dir_path(__FILE__) . 'form-basic.php';
		require_once plugin_dir_path(__FILE__) . 'text/text.php';
		require_once plugin_dir_path(__FILE__) . 'email/email.php';
		require_once plugin_dir_path(__FILE__) . 'number/number.php';
		require_once plugin_dir_path(__FILE__) . 'telephone/telephone.php';
		require_once plugin_dir_path(__FILE__) . 'date/date.php';
		require_once plugin_dir_path(__FILE__) . 'time/time.php';
		require_once plugin_dir_path(__FILE__) . 'select/select.php';
		require_once plugin_dir_path(__FILE__) . 'multi-select/multi-select.php';
		require_once plugin_dir_path(__FILE__) . 'textarea/textarea.php';
		require_once plugin_dir_path(__FILE__) . 'checkbox/checkbox.php';
		require_once plugin_dir_path(__FILE__) . 'radio/radio.php';
		require_once plugin_dir_path(__FILE__) . 'switch/switch.php';
		require_once plugin_dir_path(__FILE__) . 'range/range.php';
		require_once plugin_dir_path(__FILE__) . 'url/url.php';
		require_once plugin_dir_path(__FILE__) . 'password/password.php';
		require_once plugin_dir_path(__FILE__) . 'gdpr-consent/gdpr-consent.php';
		require_once plugin_dir_path(__FILE__) . 'recaptcha/recaptcha.php';
		require_once plugin_dir_path(__FILE__) . 'simple-captcha/simple-captcha.php';
		require_once plugin_dir_path(__FILE__) . 'simple-message/simple-message.php';
		require_once plugin_dir_path(__FILE__) . 'rating/rating.php';
		require_once plugin_dir_path(__FILE__) . 'file-upload/file-upload.php';
		require_once plugin_dir_path(__FILE__) . 'summary/summary.php';
		require_once plugin_dir_path(__FILE__) . 'button/button.php';
		require_once plugin_dir_path(__FILE__) . 'widget-notice.php';
	}

	public function register_widgets() {

        $this->includes();

		\Elementor\Plugin::instance()->widgets_manager->register( new \Elementor\MetCraft_Form() );
		\Elementor\Plugin::instance()->widgets_manager->register( new \Elementor\MetCraft_Form_Basic() );

		\Elementor\Plugin::instance()->widgets_manager->register( new \Elementor\MetCraft_Input_Button() );
		\Elementor\Plugin::instance()->widgets_manager->register( new \Elementor\MetCraft_Input_Text() );
		\Elementor\Plugin::instance()->widgets_manager->register( new \Elementor\MetCraft_Input_Email() );
		\Elementor\Plugin::instance()->widgets_manager->register( new \Elementor\MetCraft_Input_Number() );
		\Elementor\Plugin::instance()->widgets_manager->register( new \Elementor\MetCraft_Input_Telephone() );
		\Elementor\Plugin::instance()->widgets_manager->register( new \Elementor\MetCraft_Input_Date() );
		\Elementor\Plugin::instance()->widgets_manager->register( new \Elementor\MetCraft_Input_Time() );
		\Elementor\Plugin::instance()->widgets_manager->register( new \Elementor\MetCraft_Input_Select() );
		\Elementor\Plugin::instance()->widgets_manager->register( new \Elementor\MetCraft_Input_Multi_Select() );
		\Elementor\Plugin::instance()->widgets_manager->register( new \Elementor\MetCraft_Input_Textarea() );
		\Elementor\Plugin::instance()->widgets_manager->register( new \Elementor\MetCraft_Input_Checkbox() );
		\Elementor\Plugin::instance()->widgets_manager->register( new \Elementor\MetCraft_Input_Radio() );
		\Elementor\Plugin::instance()->widgets_manager->register( new \Elementor\MetCraft_Input_Switch() );
		\Elementor\Plugin::instance()->widgets_manager->register( new \Elementor\MetCraft_Input_Range() );
		\Elementor\Plugin::instance()->widgets_manager->register( new \Elementor\MetCraft_Input_Url() );
		\Elementor\Plugin::instance()->widgets_manager->register( new \Elementor\MetCraft_Input_Password() );
		\Elementor\Plugin::instance()->widgets_manager->register( new \Elementor\MetCraft_Input_Gdpr_Consent() );
		\Elementor\Plugin::instance()->widgets_manager->register( new \Elementor\MetCraft_Input_Recaptcha() );
		\Elementor\Plugin::instance()->widgets_manager->register( new \Elementor\MetCraft_Input_Simple_Captcha() );
		\Elementor\Plugin::instance()->widgets_manager->register( new \Elementor\MetCraft_Simple_Message() );
		\Elementor\Plugin::instance()->widgets_manager->register( new \Elementor\MetCraft_Input_Rating() );
		\Elementor\Plugin::instance()->widgets_manager->register( new \Elementor\MetCraft_Input_File_Upload() );
		\Elementor\Plugin::instance()->widgets_manager->register( new \Elementor\MetCraft_Input_Summary() );
	}

	public function add_metcraft_widget_categories( $elements_manager ) {

		$elements_manager->add_category(
			'metcraft',
			[
				'title' => esc_html__( 'MetCraft', 'metcraft' ),
				'icon' => 'fa fa-plug',
			]
		);
	}

}
