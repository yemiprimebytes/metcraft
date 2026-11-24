<?php
namespace Elementor;

use \MetCraft\Widgets\Input_Base;

defined( 'ABSPATH' ) || exit;

class MetCraft_Input_Radio extends MetCraft_Input_Base {

	public function get_name() {
		return 'mcf-radio';
	}

	public function get_title() {
		return esc_html__( 'Radio', 'metcraft' );
	}

	public function get_keywords() {
		return [ 'metcraft', 'radio' ];
	}

	protected function register_controls() {
		$this->add_common_controls();

		$this->start_controls_section(
			'content_section',
			[ 'label' => esc_html__( 'Content', 'metcraft' ), 'tab' => Controls_Manager::TAB_CONTENT ]
		);

		$repeater = new \Elementor\Repeater();
		$repeater->add_control( 'option_label', [ 'label' => esc_html__( 'Label', 'metcraft' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Option', 'metcraft' ), 'label_block' => true ] );
		$repeater->add_control( 'option_value', [ 'label' => esc_html__( 'Value', 'metcraft' ), 'type' => Controls_Manager::TEXT, 'default' => '', 'label_block' => true ] );

		$this->add_control(
			'options',
			[
				'label'   => esc_html__( 'Options', 'metcraft' ),
				'type'    => Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'default' => [ [ 'option_label' => 'Option 1', 'option_value' => 'option1' ], [ 'option_label' => 'Option 2', 'option_value' => 'option2' ] ],
				'title_field' => '{{{ option_label }}}',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$key = $settings['field_key'] ? $settings['field_key'] : 'mcf_radio_' . $this->get_id();
		$options = isset( $settings['options'] ) ? $settings['options'] : [];
		?>
		<div class="mcf-field-wrapper mcf-field-radio" id="mcf-field-<?php echo esc_attr( $this->get_id() ); ?>">
			<label class="mcf-field-label"><?php echo esc_html( $settings['field_label'] ); ?>
				<?php if ( 'yes' === $settings['field_required'] ) : ?>
					<span class="mcf-field-required">*</span>
				<?php endif; ?>
			</label>
			<?php foreach ( $options as $opt ) : ?>
				<label class="mcf-radio-label">
					<input type="radio" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $opt['option_value'] ?? '' ); ?>" class="mcf-field-radio" />
					<span><?php echo esc_html( $opt['option_label'] ?? '' ); ?></span>
				</label>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
