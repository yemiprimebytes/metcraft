<?php

namespace MetCraft\Core\Entries;

defined( 'ABSPATH' ) || exit;

use MetCraft\Core\Browser_Data\Capture;
use MetCraft\Core\Webhook\Delivery as Webhook_Delivery;
use MetCraft\Core\Confirmation_Email\Sender as Confirmation_Sender;
use MetCraft\Core\WooCommerce\Integration as WooCommerce_Integration;
use MetCraft\User_Auth\Manager as User_Auth_Manager;
use MetCraft\Core\Email_Verification\Verifier as Email_Verifier;
use MetCraft\Core\Redirect\Manager as Redirect_Manager;
use MetCraft\Core\Limit_Entries\Limiter;
use MetCraft\Core\Hide_Form\Controller as Hide_Form_Controller;
use MetCraft\Core\Submission_Rules\Engine as Submission_Rules_Engine;
use MetCraft\Core\Quiz\Scorer as Quiz_Scorer;

/**
 * Base class for MetCraft entries (form submissions).
 * Handles all submission features: browser data, webhooks, confirmation emails,
 * WooCommerce, user registration, email verification, redirect, limits, hide form,
 * submission rules, quiz scoring.
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
		add_action( 'admin_init', [ $this, 'register_admin_actions' ] );
		// Handle email verification callback
		add_action( 'template_redirect', [ $this, 'handle_email_verification_callback' ] );
	}

	public function register_hooks() {
		add_action( 'metcraft_form_submitted', [ $this, 'handle_submission' ], 10, 3 );
	}

	public function register_admin_actions() {
		// CSV export
		add_action( 'admin_action_metcraft_export_csv', [ $this, 'admin_export_csv' ] );
		// PDF export
		add_action( 'admin_action_metcraft_export_pdf', [ $this, 'admin_export_pdf' ] );
	}

	/**
	 * Handle a complete form submission with all feature integrations.
	 *
	 * @param int $form_id Form ID.
	 * @param array $entry_data Submitted field data.
	 * @param array $files Uploaded files.
	 * @return int|false Entry ID or false.
	 */
	public function handle_submission( $form_id, $entry_data, $files ) {
		$form = get_post( $form_id );
		if ( ! $form ) {
			return false;
		}

		$settings = get_post_meta( $form_id, '_metcraft_form_settings', true );
		if ( ! is_array( $settings ) ) {
			$settings = [];
		}

		// 1. Required login check
		\MetCraft\Core\Required_Login\Checker::can_access( $settings );

		// 2. Submission rules check
		$rules_check = Submission_Rules_Engine::can_submit( $settings );
		if ( ! $rules_check['allowed'] ) {
			return false;
		}

		// 3. Limit entries check
		$limit_check = Limiter::can_submit( $form_id, $settings );
		if ( ! $limit_check['allowed'] ) {
			return false;
		}

		// 4. Browser data capture
		$browser_data = Capture::capture();

		// 5. Email verification check
		if ( Email_Verifier::is_required( $settings ) ) {
			$email_field = $settings['email_verification_email_field'] ?? 'email';
			$email = isset( $entry_data[ $email_field ] ) ? $entry_data[ $email_field ] : '';
			if ( ! empty( $email ) && is_email( $email ) ) {
				$token = Email_Verifier::generate_token( $email );
				Email_Verifier::store( $token, $entry_data, 3600 );
				Email_Verifier::send_verification_email( $email, $token, $form_id );
				return false; // Wait for verification
			}
		}

		// 6. User registration
		if ( User_Auth_Manager::is_registration_enabled( $settings ) ) {
			$user_id = User_Auth_Manager::register_user( $settings, $entry_data );
			if ( is_wp_error( $user_id ) ) {
				return false;
			}
			$entry_data['_metcraft_user_id'] = $user_id;
		}

		// 7. User login
		if ( User_Auth_Manager::is_login_enabled( $settings ) ) {
			$login_result = User_Auth_Manager::login_user( $entry_data, $settings );
			if ( is_wp_error( $login_result ) ) {
				return false;
			}
		}

		// 8. Insert the entry
		$entry_id = wp_insert_post( [
			'post_title'   => sprintf( __( 'Entry #%d', 'metcraft' ), time() ),
			'post_type'    => 'metcraft-entry',
			'post_status'  => 'publish',
			'post_author'  => get_current_user_id() ?: 0,
			'post_content' => wp_json_encode( $entry_data ),
		] );

		if ( is_wp_error( $entry_id ) ) {
			return false;
		}

		// Store form reference and entry data as meta
		update_post_meta( $entry_id, '_metcraft_form_id', $form_id );
		update_post_meta( $entry_id, '_metcraft_entry_data', $entry_data );
		update_post_meta( $entry_id, '_metcraft_submitted_at', current_time( 'mysql' ) );
		update_post_meta( $entry_id, '_metcraft_user_ip', sanitize_text_field( $_SERVER['REMOTE_ADDR'] ?? '' ) );
		update_post_meta( $entry_id, '_metcraft_user_agent', sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ?? '' ) );
		update_post_meta( $entry_id, '_metcraft_browser_data', $browser_data );

		if ( ! empty( $files ) ) {
			update_post_meta( $entry_id, '_metcraft_entry_files', $files );
		}

		// Auto-increment entry number
		do_action( 'metcraft/entry/created', $entry_id, $form_id );

		// 9. WooCommerce order
		if ( WooCommerce_Integration::is_active() ) {
			WooCommerce_Integration::create_order( $form_id, $entry_data, $entry_id );
		}

		// 10. Webhook delivery
		Webhook_Delivery::send( $settings, $form_id, $entry_data, $entry_id );

		// 11. User confirmation email
		Confirmation_Sender::send( $settings, $entry_data, $form_id );

		// Send admin notification email
		$this->send_admin_notification( $form_id, $entry_id, $entry_data );

		// Track analytics
		do_action( 'metcraft_form_submitted', $form_id, $entry_data );

		// 12. Quiz scoring (store score in meta)
		$form_data = \MetCraft\Core\Forms\Base::instance()->get_form( $form_id );
		if ( $form_data ) {
			$score = Quiz_Scorer::calculate_score( $form_data['fields'], $entry_data );
			update_post_meta( $entry_id, '_metcraft_quiz_score', $score );
		}

		return $entry_id;
	}

	/**
	 * Handle email verification callback (template_redirect).
	 */
	public function handle_email_verification_callback() {
		if ( ! isset( $_GET['mcf_verify'] ) || ! isset( $_GET['mcf_form_id'] ) ) {
			return;
		}

		$token = sanitize_text_field( $_GET['mcf_verify'] );
		$form_id = absint( $_GET['mcf_form_id'] );

		$data = Email_Verifier::consume( $token );
		if ( $data === false ) {
			wp_die( esc_html__( 'Invalid or expired verification link.', 'metcraft' ) );
		}

		// Process the submission now that email is verified
		$this->handle_submission( $form_id, $data, [] );

		$redirect_url = \MetCraft\Core\Redirect\Manager::get_url( get_post_meta( $form_id, '_metcraft_form_settings', true ) );
		if ( ! empty( $redirect_url ) ) {
			wp_safe_redirect( $redirect_url );
		} else {
			echo esc_html__( 'Email verified. Your submission is complete.', 'metcraft' );
		}
		exit;
	}

	/**
	 * Admin action: Export entries as CSV.
	 */
	public function admin_export_csv() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized', 'metcraft' ) );
		}

		$form_id = absint( $_GET['form_id'] ?? 0 );
		\MetCraft\Core\Export_CSV\Exporter::download( $form_id );
	}

	/**
	 * Admin action: Export single entry as PDF.
	 */
	public function admin_export_pdf() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized', 'metcraft' ) );
		}

		$entry_id = absint( $_GET['entry_id'] ?? 0 );
		$filepath = \MetCraft\Core\PDF_Export\Exporter::export( $entry_id );

		if ( empty( $filepath ) ) {
			wp_die( esc_html__( 'Failed to generate PDF.', 'metcraft' ) );
		}

		if ( file_exists( $filepath ) ) {
			header( 'Content-Type: application/pdf' );
			header( 'Content-Disposition: attachment; filename="' . basename( $filepath ) . '"' );
			header( 'Content-Length: ' . filesize( $filepath ) );
			readfile( $filepath );
			exit;
		}

		wp_die( esc_html__( 'PDF not found.', 'metcraft' ) );
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
				'id'               => $entry->ID,
				'form_id'          => (int) get_post_meta( $entry->ID, '_metcraft_form_id', true ),
				'title'            => $entry->post_title,
				'date'             => $entry->post_modified,
				'data'             => is_array( $data ) ? $data : [],
				'ip'               => get_post_meta( $entry->ID, '_metcraft_user_ip', true ),
				'browser_data'     => get_post_meta( $entry->ID, '_metcraft_browser_data', true ),
				'quiz_score'       => get_post_meta( $entry->ID, '_metcraft_quiz_score', true ),
				'woocommerce_order'=> get_post_meta( $entry->ID, '_metcraft_woocommerce_order_id', true ),
			];
		}

		return $list;
	}

	public function delete_entry( $entry_id ) {
		return wp_delete_post( $entry_id, true );
	}

}
