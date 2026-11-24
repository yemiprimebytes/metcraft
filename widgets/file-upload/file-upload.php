<?php
namespace Elementor;

use \MetCraft\Widgets\Input_Base;

defined( 'ABSPATH' ) || exit;

class MetCraft_Input_File_Upload extends MetCraft_Input_Base {

	public function get_name() {
		return 'mcf-file-upload';
	}

	public function get_title() {
		return esc_html__( 'File Upload', 'metcraft' );
	}

	public function get_keywords() {
		return [ 'metcraft', 'file', 'upload', 'attachment' ];
	}

	protected function register_controls() {
		$this->add_common_controls();

		$this->start_controls_section(
			'content_section',
			[ 'label' => esc_html__( 'Content', 'metcraft' ), 'tab' => Controls_Manager::TAB_CONTENT ]
		);

		$this->add_control(
			'allowed_types',
			[ 'label' => esc_html__( 'Allowed File Types', 'metcraft' ), 'type' => Controls_Manager::TEXT, 'default' => 'jpg,jpeg,png,pdf,doc,docx', 'description' => esc_html__( 'Comma-separated file extensions.', 'metcraft' ), 'label_block' => true ]
		);
		$this->add_control(
			'max_size',
			[ 'label' => esc_html__( 'Max File Size (KB)', 'metcraft' ), 'type' => Controls_Manager::NUMBER, 'default' => 2048, 'label_block' => true ]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$key = $settings['field_key'] ? $settings['field_key'] : 'mcf_file_' . $this->get_id();
		$allowed = esc_attr( $settings['allowed_types'] ?? 'jpg,jpeg,png,pdf,doc,docx' );
		$max_size = absint( $settings['max_size'] ) ?: 2048;
		?>
		<div class="mcf-field-wrapper mcf-field-file-upload" id="mcf-field-<?php echo esc_attr( $this->get_id() ); ?>">
			<label class="mcf-field-label"><?php echo esc_html( $settings['field_label'] ); ?>
				<?php if ( 'yes' === $settings['field_required'] ) : ?>
					<span class="mcf-field-required">*</span>
				<?php endif; ?>
			</label>
			<input type="file" name="<?php echo esc_attr( $key ); ?>" id="<?php echo esc_attr( $key ); ?>"
				accept="<?php echo $allowed; ?>"
				data-max-size="<?php echo $max_size; ?>"
				class="mcf-field-input mcf-file-input"
				<?php echo 'yes' === $settings['field_required'] ? 'required' : ''; ?>
			/>
			<div class="mcf-file-info">
				<span class="mcf-file-types"><?php echo esc_html__( 'Allowed: ', 'metcraft' ) . $allowed; ?></span>
				<span class="mcf-file-size"><?php echo esc_html__( 'Max size: ', 'metcraft' ) . $max_size . ' KB'; ?></span>
			</div>
		</div>
		<?php
	}
}
