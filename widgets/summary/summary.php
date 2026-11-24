<?php
namespace Elementor;

use \MetCraft\Widgets\Input_Base;

defined( 'ABSPATH' ) || exit;

class MetCraft_Input_Summary extends MetCraft_Input_Base {

	public function get_name() {
		return 'mcf-summary';
	}

	public function get_title() {
		return esc_html__( 'Summary', 'metcraft' );
	}

	public function get_keywords() {
		return [ 'metcraft', 'summary', 'review' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			[ 'label' => esc_html__( 'Content', 'metcraft' ), 'tab' => Controls_Manager::TAB_CONTENT ]
		);

		$this->add_control(
			'summary_title',
			[ 'label' => esc_html__( 'Summary Title', 'metcraft' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Review Your Submission', 'metcraft' ), 'label_block' => true ]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		?>
		<div class="mcf-field-wrapper mcf-field-summary" id="mcf-field-<?php echo esc_attr( $this->get_id() ); ?>">
			<h3 class="mcf-summary-title"><?php echo esc_html( $settings['summary_title'] ?? esc_html__( 'Review Your Submission', 'metcraft' ) ); ?></h3>
			<div class="mcf-summary-data"></div>
		</div>
		<?php
	}
}
