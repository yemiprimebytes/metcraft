<?php
namespace Elementor;

use \MetCraft\Controls\Controls_Manager as MetCraft_Controls_Manager;

defined( 'ABSPATH' ) || exit;

abstract class MetCraft_Input_Base extends Widget_Base {

	public function get_icon() {
		return 'eicon-form-field';
	}

	public function show_in_panel() {
		return 'metcraft-form' != get_post_type();
	}

	public function get_categories() {
		return [ 'metcraft' ];
	}

	protected function add_common_controls() {
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Content', 'metcraft' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'field_label',
			[
				'label' => esc_html__( 'Label', 'metcraft' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [ 'active' => true ],
				'default' => esc_html__( 'Field Label', 'metcraft' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'field_key',
			[
				'label' => esc_html__( 'Field Key', 'metcraft' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'label_block' => true,
				'description' => esc_html__( 'Unique key used to store and identify this field value.', 'metcraft' ),
			]
		);

		$this->add_control(
			'field_placeholder',
			[
				'label' => esc_html__( 'Placeholder', 'metcraft' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [ 'active' => true ],
				'default' => '',
				'label_block' => true,
			]
		);

		$this->add_control(
			'field_required',
			[
				'label' => esc_html__( 'Required', 'metcraft' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
				'return_value' => 'yes',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_section',
			[
				'label' => esc_html__( 'Style', 'metcraft' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'advanced_section',
			[
				'label' => esc_html__( 'Advanced', 'metcraft' ),
				'tab' => Controls_Manager::TAB_ADVANCED,
			]
		);

		$this->end_controls_section();
	}

}
