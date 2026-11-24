<?php
namespace Elementor;

use \MetCraft\Widgets\Input_Base;

defined( 'ABSPATH' ) || exit;

class MetCraft_Input_Button extends MetCraft_Input_Base {

	public function get_name() {
		return 'mcf-button';
	}

	public function get_title() {
		return esc_html__( 'Submit Button', 'metcraft' );
	}

	public function get_keywords() {
		return [ 'metcraft', 'button', 'submit' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			[ 'label' => esc_html__( 'Content', 'metcraft' ), 'tab' => Controls_Manager::TAB_CONTENT ]
		);

		$this->add_control(
			'button_text',
			[ 'label' => esc_html__( 'Button Text', 'metcraft' ), 'type' => Controls_Manager::TEXT, 'dynamic' => [ 'active' => true ], 'default' => esc_html__( 'Submit', 'metcraft' ), 'label_block' => true ]
		);
		$this->add_control(
			'button_type',
			[ 'label' => esc_html__( 'Button Type', 'metcraft' ), 'type' => Controls_Manager::SELECT, 'default' => 'submit', 'options' => [ 'submit' => esc_html__( 'Submit', 'metcraft' ), 'reset' => esc_html__( 'Reset', 'metcraft') ], 'label_block' => true ]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		?>
		<div class="mcf-field-wrapper mcf-field-button" id="mcf-field-<?php echo esc_attr( $this->get_id() ); ?>">
			<button type="<?php echo esc_attr( $settings['button_type'] ?? 'submit' ); ?>" class="mcf-field-input mcf-submit-btn">
				<?php echo esc_html( $settings['button_text'] ?? esc_html__( 'Submit', 'metcraft' ) ); ?>
			</button>
		</div>
		<?php
	}
}
