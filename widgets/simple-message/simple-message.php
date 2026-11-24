<?php
namespace Elementor;

use \MetCraft\Widgets\Input_Base;

defined( 'ABSPATH' ) || exit;

class MetCraft_Simple_Message extends Input_Base {

	public function get_name() {
		return 'mcf-simple-message';
	}

	public function get_title() {
		return esc_html__( 'Simple Message', 'metcraft' );
	}

	public function get_keywords() {
		return [ 'metcraft', 'message', 'text', 'html' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			[ 'label' => esc_html__( 'Content', 'metcraft' ), 'tab' => Controls_Manager::TAB_CONTENT ]
		);

		$this->add_control(
			'message_content',
			[ 'label' => esc_html__( 'Message', 'metcraft' ), 'type' => Controls_Manager::WYSIWYG, 'default' => esc_html__( 'Your message here', 'metcraft' ), 'label_block' => true ]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		?>
		<div class="mcf-field-wrapper mcf-field-simple-message" id="mcf-field-<?php echo esc_attr( $this->get_id() ); ?>">
			<?php echo wp_kses_post( $settings['message_content'] ?? '' ); ?>
		</div>
		<?php
	}
}
