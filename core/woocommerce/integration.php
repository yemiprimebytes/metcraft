<?php

namespace MetCraft\Core\WooCommerce;

defined( 'ABSPATH' ) || exit;

/**
 * WooCommerce Integration for MetCraft forms.
 * Enables using MetCraft forms as WooCommerce checkout.
 */
class Integration {

	/**
	 * Check if WooCommerce is active.
	 *
	 * @return bool
	 */
	public static function is_active() {
		return class_exists( 'WooCommerce' ) || class_exists( 'WC_Form_Handler' );
	}

	/**
	 * Get WooCommerce settings for a form.
	 *
	 * @param array $settings Form settings.
	 * @return array
	 */
	public static function get_config( $settings ) {
		return [
			'enabled'         => ! empty( $settings['woocommerce_enabled'] ),
			'checkout_fields' => $settings['woocommerce_checkout_fields'] ?? [],
			'product_id'      => absint( $settings['woocommerce_product_id'] ?? 0 ),
		];
	}

	/**
	 * Process form submission as WooCommerce order.
	 *
	 * @param int $form_id Form ID.
	 * @param array $data Form data.
	 * @param int $entry_id Entry ID.
	 * @return int|false Order ID or false.
	 */
	public static function create_order( $form_id, $data, $entry_id ) {
		if ( ! self::is_active() ) {
			return false;
		}

		$settings = get_post_meta( $form_id, '_metcraft_form_settings', true );
		$config   = self::get_config( $settings );

		if ( empty( $config['enabled'] ) ) {
			return false;
		}

		$customer_id = get_current_user_id();
		if ( $customer_id === 0 ) {
			// Create guest customer
			$email = isset( $data['email'] ) ? $data['email'] : '';
			if ( ! empty( $email ) && is_email( $email ) ) {
				$customer_id = wc_create_new_customer( $email, wp_generate_password(), '' );
			}
		}

		$order = wc_create_order();

		if ( $config['product_id'] ) {
			$order->add_product( get_product( $config['product_id'] ), 1 );
		}

		$order->set_customer_id( $customer_id );
		$order->set_address( [
			'first_name' => isset( $data['first_name'] ) ? $data['first_name'] : '',
			'last_name'  => isset( $data['last_name'] ) ? $data['last_name'] : '',
			'company'    => isset( $data['company'] ) ? $data['company'] : '',
			'address_1'  => isset( $data['address'] ) ? $data['address'] : '',
			'city'       => isset( $data['city'] ) ? $data['city'] : '',
			'state'      => isset( $data['state'] ) ? $data['state'] : '',
			'postcode'   => isset( $data['zip'] ) ? $data['zip'] : '',
			'country'    => isset( $data['country'] ) ? $data['country'] : '',
		], 'billing' );

		$order->calculate_totals();
		$order->save();

		// Link entry to order
		update_post_meta( $entry_id, '_metcraft_woocommerce_order_id', $order->get_id() );

		return $order->get_id();
	}

}
