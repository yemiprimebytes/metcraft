<?php
namespace Elementor;

use \MetCraft\Widgets\Input_Base;

defined( 'ABSPATH' ) || exit;

class MetCraft_Input_Simple_Captcha extends Input_Base {

	public function get_name() {
		return 'mcf-simple-captcha';
	}

	public function get_title() {
		return esc_html__( 'Simple Captcha', 'metcraft' );
	}

	public function get_keywords() {
		return [ 'metcraft', 'captcha', 'math', 'spam' ];
	}

	protected function register_controls() {
		$this->add_common_controls();

		$this->start_controls_section(
			'content_section',
			[ 'label' => esc_html__( 'Content', 'metcraft' ), 'tab' => Controls_Manager::TAB_CONTENT ]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$key = $settings['field_key'] ? $settings['field_key'] : 'mcf_captcha_' . $this->get_id();
		$num1 = rand( 1, 9 );
		$num2 = rand( 1, 9 );
		$answer = $num1 + $num2;
		?>
		<div class="mcf-field-wrapper mcf-field-simple-captcha" id="mcf-field-<?php echo esc_attr( $this->get_id() ); ?>">
			<label class="mcf-field-label"><?php echo esc_html__( 'Captcha Verification', 'metcraft' ); ?>
				<span class="mcf-field-required">*</span>
			</label>
			<div class="mcf-captcha-question">
				<?php echo esc_html( $num1 . ' + ' . $num2 . ' = ?' ); ?>
				<input type="hidden" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $answer ); ?>" />
				<input type="text" name="<?php echo esc_attr( $key . '_user' ); ?>" class="mcf-field-input" required />
			</div>
		</div>
		<?php
	}
}
