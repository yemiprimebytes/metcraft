<?php

namespace MetCraft\Utils;

defined( 'ABSPATH' ) || exit;

/**
 * Utility functions for MetCraft.
 */
class Util {

	/**
	 * Render form content for frontend display.
	 *
	 * @param array $form_data Form data array.
	 * @param string $widget_id Unique widget ID.
	 * @return string
	 */
	public static function metcraft_content_renderer( $form_data, $widget_id = '' ) {
		if ( empty( $form_data ) || ! is_array( $form_data ) ) {
			return;
		}

		$form_id   = $form_data['id'] ?? 0;
		$title     = $form_data['title'] ?? '';
		$fields    = $form_data['fields'] ?? [];
		$settings  = $form_data['settings'] ?? [];
		$widget_id = $widget_id ?: uniqid( 'mcf-' );

		// Required login check
		if ( ! \MetCraft\Core\Required_Login\Checker::can_access( $settings ) ) {
			echo '<div class="mcf-required-login">' . esc_html( \MetCraft\Core\Required_Login\Checker::get_message( $settings ) ) . '<br>';
			echo '<a href="' . esc_url( \MetCraft\Core\Required_Login\Checker::get_login_url() ) . '" class="mcf-button-primary">' . esc_html__( 'Login', 'metcraft' ) . '</a></div>';
			return;
		}

		// Submission rules check
		$rules_check = \MetCraft\Core\Submission_Rules\Engine::can_submit( $settings );
		if ( ! $rules_check['allowed'] ) {
			echo '<div class="mcf-rules-blocked">' . esc_html( $rules_check['message'] ) . '</div>';
			return;
		}

		// Limit entries check
		$limit_check = \MetCraft\Core\Limit_Entries\Limiter::can_submit( $form_id, $settings );
		if ( ! $limit_check['allowed'] ) {
			echo '<div class="mcf-limit-blocked">' . esc_html( $limit_check['message'] ) . '</div>';
			return;
		}

		// Hide form after submission (check via a submitted entry cookie/session)
		$hide_form = \MetCraft\Core\Hide_Form\Controller::should_hide( $settings );
		$hide_key  = 'metcraft_hidden_form_' . $form_id;
		if ( $hide_form && isset( $_COOKIE[ $hide_key ] ) ) {
			echo '<div class="mcf-form-hidden">' . esc_html( \MetCraft\Core\Hide_Form\Controller::get_hidden_message( $settings ) ) . '</div>';
			return;
		}

		// Check schedule
		$schedule_start = isset( $settings['schedule_start'] ) ? $settings['schedule_start'] : '';
		$schedule_end   = isset( $settings['schedule_end'] ) ? $settings['schedule_end'] : '';
		$now = current_time( 'mysql' );

		if ( $schedule_start && $now < $schedule_start ) {
			echo esc_html__( 'This form is not yet available.', 'metcraft' );
			return;
		}
		if ( $schedule_end && $now > $schedule_end ) {
			echo esc_html__( 'This form is no longer accepting submissions.', 'metcraft' );
			return;
		}

		// Get reCAPTCHA HTML
		$recaptcha_html = \MetCraft\Core\Integrations\Google_Recaptcha::get_html( $form_id, $widget_id );

		// Generate nonce for AJAX submission
		$nonce = wp_create_nonce( 'metcraft_form_submit' );

		$success_message = $settings['success_message'] ?? esc_html__( 'Thank you for your submission!', 'metcraft' );
		$response_type   = $settings['response_type'] ?? 'alert';

		?>
		<form id="mcf-form-<?php echo esc_attr( $widget_id ); ?>" class="mcf-form" data-form-id="<?php echo absint( $form_id ); ?>" data-widget-id="<?php echo esc_attr( $widget_id ); ?>" data-nonce="<?php echo esc_attr( $nonce ); ?>" data-response-type="<?php echo esc_attr( $response_type ); ?>">
			<?php if ( $title ) : ?>
				<h2 class="mcf-form-title"><?php echo esc_html( $title ); ?></h2>
			<?php endif; ?>

			<div class="mcf-fields-container">
				<?php self::render_fields( $fields ); ?>
			</div>

			<?php if ( $recaptcha_html ) : ?>
				<div class="mcf-recaptcha-container"><?php echo $recaptcha_html; ?></div>
			<?php endif; ?>

			<div class="mcf-response-message" id="mcf-response-<?php echo esc_attr( $widget_id ); ?>" style="display:none;"></div>

			<div class="mcf-submit-container">
				<button type="submit" class="mcf-submit-btn mcf-button-primary"><?php esc_html_e( 'Submit', 'metcraft' ); ?></button>
			</div>

			<input type="hidden" name="mcf_form_id" value="<?php echo absint( $form_id ); ?>" />
			<input type="hidden" name="mcf_widget_id" value="<?php echo esc_attr( $widget_id ); ?>" />
		</form>

		<script>
			( function() {
				var form = document.getElementById( 'mcf-form-<?php echo esc_attr( $widget_id ); ?>' );
				if ( ! form ) return;

				form.addEventListener( 'submit', function( e ) {
					e.preventDefault();

					var btn = form.querySelector( '.mcf-submit-btn' );
					var originalText = btn.textContent;
					btn.textContent = '<?php esc_html_e( 'Submitting...', 'metcraft' ); ?>';
					btn.disabled = true;

					var formData = new FormData( form );

					fetch( '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>', {
						method: 'POST',
						body: formData
					} )
					.then( function( response ) { return response.json(); } )
					.then( function( data ) {
						var responseEl = document.getElementById( 'mcf-response-<?php echo esc_attr( $widget_id ); ?>' );
						btn.textContent = originalText;
						btn.disabled = false;

						if ( data.success ) {
							responseEl.style.display = 'block';
							responseEl.className = 'mcf-response-message mcf-response-success';
							responseEl.textContent = '<?php echo esc_js( $success_message ); ?>';
							form.reset();
							if ( typeof grecaptcha !== 'undefined' ) { grecaptcha.reset(); }
						} else {
							responseEl.style.display = 'block';
							responseEl.className = 'mcf-response-message mcf-response-error';
							responseEl.textContent = data.data && data.data.message ? data.data.message : '<?php esc_html_e( 'Submission failed. Please try again.', 'metcraft' ); ?>';
						}
					} )
					.catch( function() {
						btn.textContent = originalText;
						btn.disabled = false;
						var responseEl = document.getElementById( 'mcf-response-<?php echo esc_attr( $widget_id ); ?>' );
						responseEl.style.display = 'block';
						responseEl.className = 'mcf-response-message mcf-response-error';
						responseEl.textContent = '<?php esc_html_e( 'Submission failed. Please try again.', 'metcraft' ); ?>';
					} );
				} );
			} )();
		</script>
		<?php
	}

	/**
	 * Render individual form fields.
	 *
	 * @param array $fields Fields array.
	 */
	private static function render_fields( $fields, $submitted_values = [] ) {
		if ( empty( $fields ) || ! is_array( $fields ) ) {
			return;
		}

		// Apply conditional logic filtering
		$fields = \MetCraft\Core\Conditional_Logic\Engine::filter_visible_fields( $fields, $submitted_values );

		// Multi-step: group fields by step
		$is_multi_step = false;
		foreach ( $fields as $field ) {
			if ( ! empty( $field['step'] ) && absint( $field['step'] ) > 1 ) {
				$is_multi_step = true;
				break;
			}
		}

		if ( $is_multi_step ) {
			$steps = \MetCraft\Core\Multi_Step\Handler::group_fields_by_step( $fields );
			$step_count = count( $steps );
			$step_index = 1;
			foreach ( $steps as $step_num => $step_fields ) {
				$active_class = ( $step_num == 1 ) ? ' mcf-step-active' : '';
				echo '<div class="mcf-step' . esc_attr( $active_class ) . '" data-step="' . esc_attr( $step_num ) . '">';
				echo '<div class="mcf-step-header">' . esc_html__( 'Step', 'metcraft' ) . ' ' . esc_html( $step_index ) . ' of ' . esc_html( $step_count ) . '</div>';
				foreach ( $step_fields as $field ) {
					self::render_single_field( $field );
				}
				echo '</div>';
				$step_index++;
			}
			// Step navigation
			echo '<div class="mcf-step-nav">';
			echo '<button type="button" class="mcf-step-btn mcf-step-prev" disabled>' . esc_html__( 'Previous', 'metcraft' ) . '</button>';
			echo '<button type="button" class="mcf-step-btn mcf-step-next">' . esc_html__( 'Next', 'metcraft' ) . '</button>';
			echo '</div>';
		} else {
			foreach ( $fields as $field ) {
				self::render_single_field( $field );
			}
		}
	}

	private static function render_single_field( $field ) {
			$key  = $field['key'] ?? '';
			$label = $field['label'] ?? '';

			echo '<div class="mcf-field-wrapper mcf-field-' . esc_attr( $type ) . '" id="mcf-field-' . esc_attr( $key ) . '">';
			echo '<label class="mcf-field-label">' . esc_html( $label );
			if ( ! empty( $field['required'] ) ) {
				echo ' <span class="mcf-field-required">*</span>';
			}
			echo '</label>';

			switch ( $type ) {
				case 'text':
				case 'email':
				case 'tel':
				case 'url':
				case 'password':
					echo '<input type="' . esc_attr( $type ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" placeholder="' . esc_attr( $field['placeholder'] ?? '' ) . '" value="' . esc_attr( $field['default_value'] ?? '' ) . '" class="mcf-field-input" ' . ( ! empty( $field['required'] ) ? 'required' : '' ) . ' />';
					break;
				case 'number':
					echo '<input type="number" name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" placeholder="' . esc_attr( $field['placeholder'] ?? '' ) . '" value="' . esc_attr( $field['default_value'] ?? '' ) . '" min="' . esc_attr( $field['min_value'] ?? '' ) . '" max="' . esc_attr( $field['max_value'] ?? '' ) . '" class="mcf-field-input" ' . ( ! empty( $field['required'] ) ? 'required' : '' ) . ' />';
					break;
				case 'date':
					echo '<input type="date" name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" placeholder="' . esc_attr( $field['placeholder'] ?? '' ) . '" value="' . esc_attr( $field['default_value'] ?? '' ) . '" class="mcf-field-input mcf-datepicker" ' . ( ! empty( $field['required'] ) ? 'required' : '' ) . ' />';
					break;
				case 'time':
					echo '<input type="time" name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" placeholder="' . esc_attr( $field['placeholder'] ?? '' ) . '" value="' . esc_attr( $field['default_value'] ?? '' ) . '" class="mcf-field-input" ' . ( ! empty( $field['required'] ) ? 'required' : '' ) . ' />';
					break;
				case 'select':
					echo '<select name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" class="mcf-field-input" ' . ( ! empty( $field['required'] ) ? 'required' : '' ) . '>';
					foreach ( ( $field['options'] ?? [] ) as $opt ) {
						echo '<option value="' . esc_attr( $opt['option_value'] ?? '' ) . '">' . esc_html( $opt['option_label'] ?? '' ) . '</option>';
					}
					echo '</select>';
					break;
				case 'multi-select':
					foreach ( ( $field['options'] ?? [] ) as $opt ) {
						echo '<label class="mcf-checkbox-label"><input type="checkbox" name="' . esc_attr( $key ) . '[]" value="' . esc_attr( $opt['option_value'] ?? '' ) . '" class="mcf-field-checkbox" /> <span>' . esc_html( $opt['option_label'] ?? '' ) . '</span></label>';
					}
					break;
				case 'textarea':
					echo '<textarea name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" placeholder="' . esc_attr( $field['placeholder'] ?? '' ) . '" rows="' . absint( $field['rows'] ?? 4 ) . '" class="mcf-field-input" ' . ( ! empty( $field['required'] ) ? 'required' : '' ) . '>' . esc_html( $field['default_value'] ?? '' ) . '</textarea>';
					break;
				case 'checkbox':
				case 'switch':
					echo '<label class="mcf-checkbox-label"><input type="checkbox" name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" value="yes" class="mcf-field-checkbox" ' . ( ! empty( $field['required'] ) ? 'required' : '' ) . ' /> <span>' . esc_html( $label ) . '</span>';
					if ( ! empty( $field['required'] ) ) { echo ' <span class="mcf-field-required">*</span>'; }
					echo '</label>';
					break;
				case 'radio':
					foreach ( ( $field['options'] ?? [] ) as $opt ) {
						echo '<label class="mcf-radio-label"><input type="radio" name="' . esc_attr( $key ) . '" value="' . esc_attr( $opt['option_value'] ?? '' ) . '" class="mcf-field-radio" /> <span>' . esc_html( $opt['option_label'] ?? '' ) . '</span></label>';
					}
					break;
				case 'range':
					echo '<input type="range" name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" min="' . esc_attr( $field['min_value'] ?? 0 ) . '" max="' . esc_attr( $field['max_value'] ?? 100 ) . '" step="' . esc_attr( $field['step_value'] ?? 1 ) . '" class="mcf-field-input mcf-range-slider" /><output class="mcf-range-value" for="' . esc_attr( $key ) . '">' . esc_attr( $field['min_value'] ?? 0 ) . '</output>';
					break;
				case 'file-upload':
					$allowed = esc_attr( $field['allowed_types'] ?? 'jpg,jpeg,png,pdf,doc,docx' );
					$max_size = absint( $field['max_size'] ?? 2048 );
					echo '<input type="file" name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" accept="' . $allowed . '" data-max-size="' . $max_size . '" class="mcf-field-input mcf-file-input" ' . ( ! empty( $field['required'] ) ? 'required' : '' ) . ' />';
					break;
				case 'gdpr-consent':
					$consent_text = $field['consent_text'] ?? $label;
					echo '<label class="mcf-checkbox-label"><input type="checkbox" name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" value="yes" class="mcf-field-checkbox" ' . ( ! empty( $field['required'] ) ? 'required' : '' ) . ' /> <span>' . esc_html( $consent_text ) . '</span>';
					if ( ! empty( $field['required'] ) ) { echo ' <span class="mcf-field-required">*</span>'; }
					echo '</label>';
					break;
				case 'rating':
					$max = absint( $field['max_rating'] ?? 5 );
					echo '<div class="mcf-rating-container">';
					for ( $i = 1; $i <= $max; $i++ ) {
						echo '<input type="radio" name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '_' . $i . '" value="' . $i . '" class="mcf-field-rating-star" />';
						echo '<label for="' . esc_attr( $key ) . '_' . $i . '" class="mcf-rating-star">' . $i . '</label>';
					}
					echo '</div>';
					break;
				case 'simple-message':
					echo wp_kses_post( $field['content'] ?? '' );
					break;
				case 'simple-captcha':
					$num1 = rand( 1, 9 );
					$num2 = rand( 1, 9 );
					$answer = $num1 + $num2;
					echo '<div class="mcf-captcha-question">' . esc_html( $num1 . ' + ' . $num2 . ' = ?' ) . '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( $answer ) . '" /><input type="text" name="' . esc_attr( $key . '_user' ) . '" class="mcf-field-input" required /></div>';
					break;
				case 'recaptcha':
					echo \MetCraft\Core\Integrations\Google_Recaptcha::get_html( 0, $key );
					break;
				case 'calculation':
					$expression = $field['expression'] ?? '';
					$default    = $field['default_value'] ?? 0;
					echo '<div class="mcf-field-wrapper mcf-field-calculation" id="mcf-field-' . esc_attr( $key ) . '">';
					echo '<label class="mcf-field-label">' . esc_html( $label );
					if ( ! empty( $field['required'] ) ) { echo ' <span class="mcf-field-required">*</span>'; }
					echo '</label>';
					echo '<input type="text" name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" value="' . esc_attr( $default ) . '" class="mcf-field-input mcf-calculation-field" data-expression="' . esc_attr( $expression ) . '" readonly />';
					echo '</div>';
					break;
				case 'quiz':
					$points = absint( $field['points'] ?? 0 );
					$options = isset( $field['options'] ) ? $field['options'] : [];
					echo '<div class="mcf-field-wrapper mcf-field-quiz" id="mcf-field-' . esc_attr( $key ) . '">';
					echo '<label class="mcf-field-label">' . esc_html( $label );
					if ( ! empty( $field['required'] ) ) { echo ' <span class="mcf-field-required">*</span>'; }
					echo ' <span class="mcf-quiz-points">(' . esc_html__( 'Points: ', 'metcraft' ) . $points . ')</span></label>';
					foreach ( $options as $opt ) {
						echo '<label class="mcf-radio-label"><input type="radio" name="' . esc_attr( $key ) . '" value="' . esc_attr( $opt['option_value'] ?? '' ) . '" class="mcf-field-radio" /> <span>' . esc_html( $opt['option_label'] ?? '' ) . '</span>';
						if ( ! empty( $opt['is_correct'] ) ) { echo ' <span class="mcf-quiz-correct" style="display:none;">✓</span>'; }
						echo '</label>';
					}
					echo '</div>';
					break;
			}

			echo '</div>';
		}
	}

	/**
	 * Render a form via shortcode or template tag.
	 *
	 * @param int $form_id Form post ID.
	 * @param string $widget_id Unique widget ID.
	 * @return string
	 */
	public static function render_form_content( $form_id, $widget_id = '' ) {
		$form_id = absint( $form_id );
		if ( ! $form_id || 'metcraft-form' !== get_post_type( $form_id ) ) {
			return esc_html__( 'No form found.', 'metcraft' );
		}

		$form_base = \MetCraft\Core\Forms\Base::instance();
		$form      = $form_base->get_form( $form_id );
		if ( ! $form ) {
			return esc_html__( 'No form found.', 'metcraft' );
		}

		ob_start();
		self::metcraft_content_renderer( $form, $widget_id );
		return ob_get_clean();
	}

	/**
	 * Setup permalinks for MetCraft post types.
	 */
	public static function permalink_setup() {
		// Ensures rewrite rules are flushed on init
	}

	/**
	 * Admin action handler for AJAX requests.
	 */
	public static function metcraft_admin_action() {
		check_ajax_referer( 'metcraft_admin_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'Unauthorized', 'metcraft' ) );
		}

		$action = sanitize_text_field( $_POST['action_data'] ?? '' );

		switch ( $action ) {
			case 'delete_entry':
				$entry_id = absint( $_POST['entry_id'] ?? 0 );
				$result   = \MetCraft\Core\Entries\Base::instance()->delete_entry( $entry_id );
				wp_send_json_success( [ 'deleted' => (bool) $result ] );
				break;
			default:
				wp_send_json_error( __( 'Unknown action', 'metcraft' ) );
		}
	}

	/**
	 * Handle frontend form submission via AJAX.
	 *
	 * @return void
	 */
	public static function metcraft_submit_form() {
		check_ajax_referer( 'metcraft_form_submit', 'nonce' );

		$form_id = absint( $_POST['mcf_form_id'] ?? 0 );
		if ( ! $form_id || 'metcraft-form' !== get_post_type( $form_id ) ) {
			wp_send_json_error( [ 'message' => __( 'Invalid form', 'metcraft' ) ] );
		}

		$form = \MetCraft\Core\Forms\Base::instance()->get_form( $form_id );
		if ( ! $form ) {
			wp_send_json_error( [ 'message' => __( 'Form not found', 'metcraft' ) ] );
		}

		$fields  = $form['fields'];
		$data    = [];
		$files   = [];

		// Collect posted fields
		foreach ( $fields as $field ) {
			$key = $field['key'] ?? '';
			if ( empty( $key ) ) {
				continue;
			}

			if ( ! empty( $field['required'] ) && ( ! isset( $_POST[ $key ] ) || $_POST[ $key ] === '' ) ) {
				wp_send_json_error( [ 'message' => sprintf( __( '%s is required.', 'metcraft' ), $field['label'] ?? $key ) ] );
			}

			if ( $field['type'] === 'file-upload' && ! empty( $_FILES[ $key ]['name'] ) ) {
				$files[ $key ] = self::handle_file_upload( $_FILES[ $key ], $field );
				$data[ $key ]  = $files[ $key]['name'] ?? '';
			} elseif ( $field['type'] === 'multi-select' || $field['type'] === 'checkbox' ) {
				$data[ $key ] = isset( $_POST[ $key ] ) ? (array) $_POST[ $key ] : [];
			} else {
				$data[ $key ] = sanitize_text_field( wp_unslash( $_POST[ $key ] ?? '' ) );
			}
		}

		// Compute calculation field values
		foreach ( $fields as $field ) {
			if ( ! empty( $field['type'] ) && $field['type'] === 'calculation' ) {
				$key = $field['key'] ?? '';
				$expression = $field['expression'] ?? '';
				if ( ! empty( $key ) && ! empty( $expression ) ) {
					$data[ $key ] = \MetCraft\Core\Calculation\Engine::evaluate( $expression, $data );
				}
			}
		}

		// Validate
		$errors = \MetCraft\Core\Entries\Validation::validate_required( $data, $fields );
		if ( ! empty( $errors ) ) {
			wp_send_json_error( [ 'message' => implode( ' ', $errors ) ] );
		}

		// Email field validation
		foreach ( $fields as $field ) {
			if ( $field['type'] === 'email' && ! empty( $data[ $field['key'] ] ?? '' ) ) {
				$email_error = \MetCraft\Core\Entries\Validation::validate_email( $data[ $field['key'] ] );
				if ( $email_error ) {
					wp_send_json_error( [ 'message' => $email_error ] );
				}
			}
		}

		// Create entry
		$entry_id = \MetCraft\Core\Entries\Base::instance()->handle_submission( $form_id, $data, $files );

		if ( ! $entry_id ) {
			wp_send_json_error( [ 'message' => __( 'Failed to save submission', 'metcraft' ) ] );
		}

		$settings = $form['settings'] ?? [];

		// Payment processing
		if ( ! empty( $settings['payment_enabled'] ) ) {
			$payment_result = self::process_payment( $settings, $data );
			if ( ! $payment_result['success'] ) {
				wp_send_json_error( [ 'message' => $payment_result['error'] ?? __( 'Payment failed.', 'metcraft' ) ] );
			}
			$data['_metcraft_payment'] = $payment_result;
		}

		do_action( 'metcraft_form_submitted', $form_id, $data );

		// Confirmation email already sent in handle_submission

		// Redirect after submission
		$redirect_url = \MetCraft\Core\Redirect\Manager::get_url( $settings );
		if ( \MetCraft\Core\Redirect\Manager::is_enabled( $settings ) && ! empty( $redirect_url ) ) {
			wp_send_json_success( [
				'message' => __( 'Form submitted successfully.', 'metcraft' ),
				'entry_id' => $entry_id,
				'redirect' => $redirect_url,
			] );
		}

		wp_send_json_success( [ 'message' => __( 'Form submitted successfully.', 'metcraft' ), 'entry_id' => $entry_id ] );
	}

	/**
	 * Process payment for a form submission.
	 *
	 * @param array $settings Form settings.
	 * @param array $data Form data.
	 * @return array
	 */
	private static function process_payment( $settings, $data ) {
		$config = \MetCraft\Core\Payment\Gateway::get_config( $settings );

		if ( empty( $config['enabled'] ) ) {
			return [ 'success' => true ];
		}

		$amount_field = $config['amount_field'] ?? '';
		$amount = 0;
		if ( ! empty( $amount_field ) && isset( $data[ $amount_field ] ) ) {
			$amount = floatval( $data[ $amount_field ] );
		}

		if ( $amount <= 0 ) {
			return [ 'success' => false, 'error' => esc_html__( 'Invalid payment amount.', 'metcraft' ) ];
		}

		$gateway = $config['gateway'] ?? 'stripe';
		$payment_token = sanitize_text_field( $_POST['mcf_payment_token'] ?? '' );

		if ( $gateway === 'paypal' ) {
			return \MetCraft\Core\Payment\Gateway::process_paypal( $config, $amount );
		}

		// Default Stripe
		if ( empty( $payment_token ) ) {
			return [ 'success' => false, 'error' => esc_html__( 'Payment token is required.', 'metcraft' ) ];
		}

		return \MetCraft\Core\Payment\Gateway::process_stripe( $config, $amount, $payment_token );
	}

	/**
	 * Handle file upload for form submission.
	 *
	 * @param array $file File data from $_FILES.
	 * @param array $field Field configuration.
	 * @return array
	 */
	private static function handle_file_upload( $file, $field ) {
		$upload_dir = wp_upload_dir();
		$allowed    = isset( $field['allowed_types'] ) ? explode( ',', $field['allowed_types'] ) : [ 'jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx' ];
		$max_size   = isset( $field['max_size'] ) ? absint( $field['max_size'] ) * 1024 : 2048 * 1024;

		$validation = \MetCraft\Core\Entries\File_Data_Validation::validate_file( $file, $allowed, $max_size );
		if ( ! empty( $validation ) ) {
			return [];
		}

		$filename = sanitize_file_name( $file['name'] );
		$unique   = uniqid() . '_' . $filename;
		$dest     = $upload_dir['path'] . '/' . $unique;

		if ( move_uploaded_file( $file['tmp_name'], $dest ) ) {
			return [
				'name'  => $filename,
				'path'  => $dest,
				'url'   => $upload_dir['url'] . '/' . $unique,
				'size'  => $file['size'],
				'type'  => $file['type'],
			];
		}

		return [];
	}

}
