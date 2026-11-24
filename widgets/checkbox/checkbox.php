<?php
namespace Elementor;

use \MetCraft\Widgets\Input_Base;

defined( 'ABSPATH' ) || exit;

class MetCraft_Input_Checkbox extends MetCraft_Input_Base {

	public function get_name() {
		return 'mcf-checkbox';
	}

	public function get_title() {
		return esc_html__( 'Checkbox', 'metcraft' );
	}

	public function get_keywords() {
		return [ 'metcraft', 'checkbox' ];
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
		$key = $settings['field_key'] ? $settings['field_key'] : 'mcf_checkbox_' . $this->get_id();
		?>
		<div class="mcf-field-wrapper mcf-field-checkbox" id="mcf-field-<?php echo esc_attr( $this->get_id() ); ?>">
			<label class="mcf-checkbox-label">
				<input type="checkbox" name="<?php echo esc_attr( $key ); ?>" id="<?php echo esc_attr( $key ); ?>"
					value="yes" class="mcf-field-checkbox"
					<?php echo 'yes' === $settings['field_required'] ? 'required' : ''; ?>
				/>
				<span><?php echo esc_html( $settings['field_label'] ); ?></span>
				<?php if ( 'yes' === $settings['field_required'] ) : ?>
					<span class="mcf-field-required">*</span>
				<?php endif; ?>
			</label>
		</div>
		<?php
	}
}
