<?php
namespace Elementor;

use \MetCraft\Widgets\Input_Base;

defined( 'ABSPATH' ) || exit;

class MetCraft_Input_Rating extends MetCraft_Input_Base {

	public function get_name() {
		return 'mcf-rating';
	}

	public function get_title() {
		return esc_html__( 'Rating', 'metcraft' );
	}

	public function get_keywords() {
		return [ 'metcraft', 'rating', 'stars' ];
	}

	protected function register_controls() {
		$this->add_common_controls();

		$this->start_controls_section(
			'content_section',
			[ 'label' => esc_html__( 'Content', 'metcraft' ), 'tab' => Controls_Manager::TAB_CONTENT ]
		);

		$this->add_control(
			'max_rating',
			[ 'label' => esc_html__( 'Max Rating', 'metcraft' ), 'type' => Controls_Manager::NUMBER, 'default' => 5, 'label_block' => true ]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$key = $settings['field_key'] ? $settings['field_key'] : 'mcf_rating_' . $this->get_id();
		$max = absint( $settings['max_rating'] ) ?: 5;
		?>
		<div class="mcf-field-wrapper mcf-field-rating" id="mcf-field-<?php echo esc_attr( $this->get_id() ); ?>">
			<label class="mcf-field-label"><?php echo esc_html( $settings['field_label'] ); ?>
				<?php if ( 'yes' === $settings['field_required'] ) : ?>
					<span class="mcf-field-required">*</span>
				<?php endif; ?>
			</label>
			<div class="mcf-rating-container">
				<?php for ( $i = 1; $i <= $max; $i++ ) : ?>
					<input type="radio" name="<?php echo esc_attr( $key ); ?>" id="<?php echo esc_attr( $key ); ?>_<?php echo $i; ?>" value="<?php echo $i; ?>" class="mcf-field-rating-star" />
					<label for="<?php echo esc_attr( $key ); ?>_<?php echo $i; ?>" class="mcf-rating-star"><?php echo $i; ?></label>
				<?php endfor; ?>
			</div>
		</div>
		<?php
	}
}
