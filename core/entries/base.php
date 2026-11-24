<?php

namespace MetCraft\Core\Entries;

defined( 'ABSPATH' ) || exit;

/**
 * Base class for MetCraft entries (form submissions).
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
		add_action( 'init', [ $this, 'register_hooks' ] );
	}

	public function register_hooks() {
		add_action( 'metcraft_form_submitted', [ $this, 'handle_submission' ], 10, 3 );
	}

	public function handle_submission( $form_id, $entry_data, $files ) {
		$form = get_post( $form_id );
		if ( ! $form ) {
			return;
		}

		$entry_id = wp_insert_post( [
			'post_title'   => sprintf( __( 'Entry #%d', 'metcraft' ), time() ),
			'post_type'    => 'metcraft-entry',
			'post_status'  => 'publish',
			'post_author'  => get_current_user_id() ?: 0,
			'post_content' => wp_json_encode( $entry_data ),
		] );

		if ( is_wp_error( $entry_id ) ) {
			return;
		}

		// Store form reference and entry data as meta
		update_post_meta( $entry_id, '_metcraft_form_id', $form_id );
		update_post_meta( $entry_id, '_metcraft_entry_data', $entry_data );
		update_post_meta( $entry_id, '_metcraft_submitted_at', current_time( 'mysql' ) );
		update_post_meta( $entry_id, '_metcraft_user_ip', sanitize_text_field( $_SERVER['REMOTE_ADDR'] ?? '' ) );
		update_post_meta( $entry_id, '_metcraft_user_agent', sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ?? '' ) );

		if ( ! empty( $files ) ) {
			update_post_meta( $entry_id, '_metcraft_entry_files', $files );
		}

		// Auto-increment entry number
		do_action( 'metcraft/entry/created', $entry_id, $form_id );

		// Send admin notification email
		$this->send_admin_notification( $form_id, $entry_id, $entry_data );

		// Track analytics
		do_action( 'metcraft_form_submitted', $form_id, $entry_data );

		return $entry_id;
	}

	public function send_admin_notification( $form_id, $entry_id, $entry_data ) {
		$form_settings = get_post_meta( $form_id, '_metcraft_form_settings', true );
		if ( ! is_array( $form_settings ) ) {
			$form_settings = [];
		}

		$admin_email = get_option( 'admin_email' );
		$subject     = sprintf( __( 'New Form Submission: %s', 'metcraft' ), get_the_title( $form_id ) );

		$message = sprintf( __( 'You have received a new submission for the form "%s".', 'metcraft' ), get_the_title( $form_id ) ) . "\n\n";
		$message .= sprintf( __( 'Entry ID: %d', 'metcraft' ), $entry_id ) . "\n";
		$message .= sprintf( __( 'Submitted at: %s', 'metcraft' ), current_time( 'mysql' ) ) . "\n\n";
		$message .= __( 'Form Data:', 'metcraft' ) . "\n";

		foreach ( $entry_data as $field_key => $field_value ) {
			if ( is_array( $field_value ) ) {
				$field_value = implode( ', ', $field_value );
			}
			$message .= "- " . esc_html( $field_key ) . ": " . esc_html( $field_value ) . "\n";
		}

		$message .= "\n" . sprintf( __( 'View entry: %s', 'metcraft' ), admin_url( 'post.php?post=' . $entry_id . '&action=edit' ) );

		$headers = [ 'Content-Type: text/plain; charset=UTF-8' ];

		wp_mail( $admin_email, $subject, $message, $headers );
	}

	public function get_entries( $form_id = '' ) {
		$args = [
			'posts_per_page' => -1,
			'post_type'      => 'metcraft-entry',
			'post_status'    => 'publish',
			'orderby'        => 'modified',
			'order'          => 'DESC',
		];

		if ( $form_id ) {
			$args['meta_query'] = [
				[
					'key'     => '_metcraft_form_id',
					'value'   => absint( $form_id ),
				],
			];
		}

		$entries = get_posts( $args );
		$list    = [];

		foreach ( $entries as $entry ) {
			$data = get_post_meta( $entry->ID, '_metcraft_entry_data', true );
			$list[] = [
				'id'       => $entry->ID,
				'form_id'  => (int) get_post_meta( $entry->ID, '_metcraft_form_id', true ),
				'title'    => $entry->post_title,
				'date'     => $entry->post_modified,
				'data'     => is_array( $data ) ? $data : [],
				'ip'       => get_post_meta( $entry->ID, '_metcraft_user_ip', true ),
			];
		}

		return $list;
	}

	public function delete_entry( $entry_id ) {
		return wp_delete_post( $entry_id, true );
	}

}
