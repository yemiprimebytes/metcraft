<?php
namespace Elementor;
defined( 'ABSPATH' ) || exit;
use MetCraft\Core\FormBuilder\Document\Repository;

class MetCraft_Form_Basic extends Widget_Base {

	public function get_name() {
		return 'metcraft-basic';
    }

	public function get_title() {
		return esc_html__( 'MetCraft Basic', 'metcraft' );
	}

	public function show_in_panel() {
        return 'metcraft-form' != get_post_type();
	}

	public function get_categories() {
		return [ 'metcraft' ];
	}

	public function get_keywords() {
        return ['metcraft', 'form'];
	}

	public function get_all_forms(){
		$form_list = [];
		$args = array(
			'posts_per_page'   => -1,
			'post_type'   => 'metcraft-form',
			'post_status' => 'publish',
		);

		$forms = get_posts( $args );

		foreach($forms as $form){
			if ( ! \MetCraft\Core\FormBuilder\Document\Repository::is_builder_form( $form->ID ) ) {
				$form_list[$form->ID] = $form->post_title;
			}
		}

		return $form_list;
	}

	protected function register_controls() {

        $this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Content', 'metcraft' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'mcf_form_id',
			[
				'label' => esc_html__( 'Select Form : ', 'metcraft' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => $this->get_all_forms(),
			]
		);

        $this->end_controls_section();
	}

	protected function render( $instance = [] ) {
		$settings = $this->get_settings_for_display();
		\MetCraft\Utils\Util::metcraft_content_renderer(\MetCraft\Utils\Util::render_form_content($settings['mcf_form_id'], $this->get_id()));
	}
}
