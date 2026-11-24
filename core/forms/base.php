<?php

namespace MetCraft\Core\Forms;

defined( 'ABSPATH' ) || exit;

/**
 * Base class for MetCraft forms management.
 */
class Base {

	private static $instance;

	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function init() {
		add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );
		add_action( 'save_post_metcraft-form', [ $this, 'save_form_data' ], 10, 2 );
		add_action( 'wp_ajax_metcraft_save_form', [ $this, 'ajax_save_form' ] );
		add_action( 'wp_ajax_nopriv_metcraft_save_form', [ $this, 'ajax_save_form' ] );
		add_action( 'wp_ajax_metcraft_delete_form', [ $this, 'ajax_delete_form' ] );
		add_action( 'wp_ajax_nopriv_metcraft_delete_form', [ $this, 'ajax_delete_form' ] );
	}

	public function add_meta_boxes() {
		add_meta_box(
			'metcraft_form_settings',
			__( 'Form Settings', 'metcraft' ),
			[ $this, 'render_settings_meta_box' ],
			'metcraft-form',
			'side',
			'default'
		);
	}

	public function render_settings_meta_box( $post ) {
		wp_nonce_field( 'metcraft_form_settings', 'metcraft_form_settings_nonce' );
		$settings = get_post_meta( $post->ID, '_metcraft_form_settings', true );
		if ( ! is_array( $settings ) ) {
			$settings = [];
		}
		?>
		<p>
			<label for="mcf_success_message"><?php esc_html_e( 'Success Message', 'metcraft' ); ?></label><br />
			<input type="text" name="metcraft_settings[success_message]" value="<?php echo esc_attr( $settings['success_message'] ?? __( 'Thank you for your submission!', 'metcraft' ) ); ?>" class="widefat" />
		</p>
		<p>
			<label for="mcf_response_type"><?php esc_html_e( 'Response Type', 'metcraft' ); ?></label><br />
			<select name="metcraft_settings[response_type]" class="widefat">
				<option value="alert" <?php selected( $settings['response_type'] ?? 'alert', 'alert' ); ?>><?php esc_html_e( 'Alert', 'metcraft' ); ?></option>
				<option value="inline" <?php selected( $settings['response_type'] ?? 'alert', 'inline' ); ?>><?php esc_html_e( 'Inline Message', 'metcraft' ); ?></option>
			</select>
		</p>
		<p>
			<label for="mcf_schedule_start"><?php esc_html_e( 'Schedule Start Date', 'metcraft' ); ?></label><br />
			<input type="datetime-local" name="metcraft_settings[schedule_start]" value="<?php echo esc_attr( $settings['schedule_start'] ?? '' ); ?>" class="widefat" />
		</p>
		<p>
			<label for="mcf_schedule_end"><?php esc_html_e( 'Schedule End Date', 'metcraft' ); ?></label><br />
			<input type="datetime-local" name="metcraft_settings[schedule_end]" value="<?php echo esc_attr( $settings['schedule_end'] ?? '' ); ?>" class="widefat" />
		</p>
		<?php
	}

	public function save_form_data( $post_id, $post ) {
		if ( ! isset( $_POST['metcraft_form_settings_nonce'] ) || ! wp_verify_nonce( $_POST['metcraft_form_settings_nonce'], 'metcraft_form_settings' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( isset( $_POST['metcraft_settings'] ) ) {
			$settings = array_map( 'sanitize_text_field', wp_unslash( $_POST['metcraft_settings'] ) );
			update_post_meta( $post_id, '_metcraft_form_settings', $settings );
		}
	}

	public function ajax_save_form() {
		check_ajax_referer( 'metcraft_form_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'Unauthorized', 'metcraft' ) );
		}

		$form_id   = absint( $_POST['form_id'] ?? 0 );
		$form_data = isset( $_POST['form_data'] ) ? (array) json_decode( wp_unslash( $_POST['form_data'] ), true ) : [];
		$title     = sanitize_text_field( $_POST['title'] ?? __( 'Untitled Form', 'metcraft' ) );

		if ( $form_id ) {
			$post = get_post( $form_id );
			if ( ! $post || 'metcraft-form' !== $post->post_type ) {
				wp_send_json_error( __( 'Invalid form', 'metcraft' ) );
			}
			wp_update_post( [
				'ID'         => $form_id,
				'post_title' => $title,
			] );
		} else {
			$form_id = wp_insert_post( [
				'post_title'   => $title,
				'post_type'    => 'metcraft-form',
				'post_status'  => 'publish',
				'post_author'  => get_current_user_id(),
			] );
		}

		if ( is_wp_error( $form_id ) ) {
			wp_send_json_error( $form_id->get_error_message() );
		}

		update_post_meta( $form_id, '_metcraft_form_data', $form_data );

		wp_send_json_success( [
			'form_id' => $form_id,
			'edit_url'=> admin_url( 'post.php?post=' . $form_id . '&action=edit' ),
		] );
	}

	public function ajax_delete_form() {
		check_ajax_referer( 'metcraft_form_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'Unauthorized', 'metcraft' ) );
		}

		$form_id = absint( $_POST['form_id'] ?? 0 );
		wp_delete_post( $form_id, true );

		wp_send_json_success( [ 'form_id' => $form_id ] );
	}

	public function get_form( $form_id ) {
		$form_id = absint( $form_id );
		if ( ! $form_id || 'metcraft-form' !== get_post_type( $form_id ) ) {
			return null;
		}

		$form_data = get_post_meta( $form_id, '_metcraft_form_data', true );
		$settings  = get_post_meta( $form_id, '_metcraft_form_settings', true );

		return [
			'id'      => $form_id,
			'title'   => get_the_title( $form_id ),
			'fields'  => isset( $form_data['fields'] ) ? $form_data['fields'] : [],
			'settings'=> is_array( $settings ) ? $settings : [],
		];
	}

	public function get_all_forms() {
		$forms = get_posts( [
			'posts_per_page' => -1,
			'post_type'      => 'metcraft-form',
			'post_status'    => 'publish',
			'orderby'        => 'modified',
			'order'          => 'DESC',
		] );

		$list = [];
		foreach ( $forms as $form ) {
			$list[] = [
				'id'    => $form->ID,
				'title' => $form->post_title,
				'date'  => $form->post_modified,
			];
		}

		return $list;
	}

}
