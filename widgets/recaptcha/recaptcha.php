<?php
namespace Elementor;

use \MetCraft\Widgets\Input_Base;

defined( 'ABSPATH' ) || exit;

class MetCraft_Input_Recaptcha extends Input_Base {

	public function get_name() {
		return 'mcf-recaptcha';
	}

	public function get_title() {
		return esc_html__( 'reCAPTCHA', 'metcraft' );
	}

	public function get_keywords() {
		return [ 'metcraft', 'recaptcha', 'captcha', 'spam' ];
	}

	protected function register_controls() {
		$this->add_common_controls();

		$this->start_controls_section(
			'content_section',
			[ 'label' => esc_html__( 'Content', 'metcraft' ), 'tab' => Controls_Manager::TAB_CONTENT ]
		);

		$this->add_control(
			'recaptcha_type',
			[ 'label' => esc_html__( 'reCAPTCHA Type', 'metcraft' ), 'type' => Controls_Manager::SELECT, 'default' => 'v2', 'options' => [ 'v2' => esc_html__( 'v2', 'metcraft' ), 'v3' => esc_html__( 'v3', 'metcraft' ) ], 'label_block' => true ]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$form_settings = get_option( 'metcraft_settings', [] );
		$site_key = isset( $form_settings['mcf_recaptcha_site_key'] ) ? $form_settings['mcf_recaptcha_site_key'] : '';
		$version = $settings['recaptcha_type'] ?? 'v2';
		?>
		<div class="mcf-field-wrapper mcf-field-recaptcha" id="mcf-field-<?php echo esc_attr( $this->get_id() ); ?>">
			<label class="mcf-field-label"><?php echo esc_html__( 'reCAPTCHA Verification', 'metcraft' ); ?></label>
			<?php if ( $version === 'v3' ) : ?>
				<div class="grecaptcha-v3" data-sitekey="<?php echo esc_attr( $site_key ); ?>" data-action="form_submit_<?php echo esc_attr( $this->get_id() ); ?>"></div>
			<?php else : ?>
				<div class="g-recaptcha" data-sitekey="<?php echo esc_attr( $site_key ); ?>"></div>
			<?php endif; ?>
		</div>
		<?php
	}
}
