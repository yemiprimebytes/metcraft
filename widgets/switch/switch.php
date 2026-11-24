<?php
namespace Elementor;

use \MetCraft\Widgets\Input_Base;

defined( 'ABSPATH' ) || exit;

class MetCraft_Input_Switch extends Input_Base {

	public function get_name() {
		return 'mcf-switch';
	}

	public function get_title() {
		return esc_html__( 'Switch', 'metcraft' );
	}

	public function get_keywords() {
		return [ 'metcraft', 'switch', 'toggle' ];
	}

	protected function register_controls() {
		$this->add_common_controls();

		$this->start_controls_section(
			'content_section',
			[ 'label' => esc_html__( 'Content', 'metcraft' ), 'tab' => Controls_Manager::TAB_CONTENT ]
		);

		$this->add_control(
			'default_value',
			[ 'label' => esc_html__( 'Default Value', 'metcraft' ), 'type' => Controls_Manager::TEXT, 'default' => '', 'label_block' => true ]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$key = $settings['field_key'] ? $settings['field_key'] : 'mcf_switch_' . $this->get_id();
		?>
		<div class="mcf-field-wrapper mcf-field-switch" id="mcf-field-<?php echo esc_attr( $this->get_id() ); ?>">
			<label class="mcf-switch-label">
				<input type="checkbox" name="<?php echo esc_attr( $key ); ?>" id="<?php echo esc_attr( $key ); ?>"
					value="yes" class="mcf-field-switch"
					<?php echo 'yes' === $settings['field_required'] ? 'required' : ''; ?>
				/>
				<span class="mcf-switch-slider"></span>
				<span><?php echo esc_html( $settings['field_label'] ); ?></span>
				<?php if ( 'yes' === $settings['field_required'] ) : ?>
					<span class="mcf-field-required">*</span>
				<?php endif; ?>
			</label>
		</div>
		<?php
	}
}
