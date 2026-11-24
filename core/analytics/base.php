<?php

namespace MetCraft\Core\Analytics;

defined( 'ABSPATH' ) || exit;

/**
 * Form analytics base for MetCraft (free feature).
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
		add_action( 'metcraft_form_submitted', [ $this, 'track_submission' ], 10, 2 );
		add_action( 'wp_ajax_metcraft_analytics', [ $this, 'ajax_analytics' ] );
		add_action( 'wp_ajax_nopriv_metcraft_analytics', [ $this, 'ajax_analytics' ] );
	}

	public function track_submission( $form_id, $entry_data ) {
		// Track submission count as post meta
		$count = (int) get_post_meta( $form_id, '_metcraft_submission_count', true );
		update_post_meta( $form_id, '_metcraft_submission_count', $count + 1 );

		// Track last submission time
		update_post_meta( $form_id, '_metcraft_last_submission', current_time( 'mysql' ) );
	}

	public function ajax_analytics() {
		check_ajax_referer( 'metcraft_analytics_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'Unauthorized', 'metcraft' ) );
		}

		$form_id = absint( $_POST['form_id'] ?? 0 );
		$stats   = [
			'submissions'   => (int) get_post_meta( $form_id, '_metcraft_submission_count', true ),
			'last_submission' => get_post_meta( $form_id, '_metcraft_last_submission', true ),
		];

		wp_send_json_success( $stats );
	}

	public function get_stats( $form_id ) {
		return [
			'submissions'   => (int) get_post_meta( $form_id, '_metcraft_submission_count', true ),
			'last_submission' => get_post_meta( $form_id, '_metcraft_last_submission', true ),
		];
	}

}
