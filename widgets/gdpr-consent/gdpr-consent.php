<?php
namespace Elementor;

use \MetCraft\Widgets\Input_Base;

defined( 'ABSPATH' ) || exit;

class MetCraft_Input_Gdpr_Consent extends MetCraft_Input_Base {

	public function get_name() {
		return 'mcf-gdpr-consent';
	}

	public function get_title() {
		return esc_html__( 'GDPR Consent', 'metcraft' );
	}

	public function get_keywords() {
		return [ 'metcraft', 'gdpr', 'consent', 'privacy' ];
	}

	protected function register_controls() {
		$this->add_common_controls();

		$this->start_controls_section(
			'content_section',
			[ 'label' => esc_html__( 'Content', 'metcraft' ), 'tab' => Controls_Manager::TAB_CONTENT ]
		);

		$this->add_control(
			'consent_text',
			[ 'label' => esc_html__( 'Consent Text', 'metcraft' ), 'type' => Controls_Manager::TEXT, 'dynamic' => [ 'active' => true ], 'default' => esc_html__( 'I agree to the terms and conditions', 'metcraft' ), 'label_block' => true ]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$key = $settings['field_key'] ? $settings['field_key'] : 'mcf_gdpr_' . $this->get_id();
		?>
		<div class="mcf-field-wrapper mcf-field-gdpr-consent" id="mcf-field-<?php echo esc_attr( $this->get_id() ); ?>">
			<label class="mcf-checkbox-label">
				<input type="checkbox" name="<?php echo esc_attr( $key ); ?>" id="<?php echo esc_attr( $key ); ?>"
					value="yes" class="mcf-field-checkbox"
					<?php echo 'yes' === $settings['field_required'] ? 'required' : ''; ?>
				/>
				<span><?php echo esc_html( $settings['consent_text'] ?? $settings['field_label'] ); ?></span>
				<?php if ( 'yes' === $settings['field_required'] ) : ?>
					<span class="mcf-field-required">*</span>
				<?php endif; ?>
			</label>
		</div>
		<?php
	}
}
