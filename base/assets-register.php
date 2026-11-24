<?php

namespace MetCraft\Base;

defined( 'ABSPATH' ) || exit;

/**
 * Registers and enqueues assets for MetCraft.
 */
class Assets_Register {

	private static $assets = [];

	public static function get( $handle ) {
		if ( isset( self::$assets[ $handle ] ) ) {
			$asset = self::$assets[ $handle ];
			if ( ! empty( $asset['deregister'] ) ) {
				wp_deregister_style( $handle );
				wp_deregister_script( $handle );
			}
			if ( ! empty( $asset['style'] ) ) {
				wp_register_style(
					$handle,
					$asset['src'],
					$asset['deps'] ?? [],
					$asset['ver'] ?? METCRAFT_VERSION,
					$asset['media'] ?? 'all'
				);
			}
			if ( ! empty( $asset['script'] ) ) {
				wp_register_script(
					$handle,
					$asset['src'],
					$asset['deps'] ?? [],
					$asset['ver'] ?? METCRAFT_VERSION,
					$asset['in_footer'] ?? true
				);
				if ( ! empty( $asset['localize'] ) ) {
					wp_localize_script( $handle, $asset['localize']['name'], $asset['localize']['data'] );
				}
			}
		}
	}

	public static function get_style( $handle ) {
		if ( isset( self::$assets[ $handle ] ) && ! empty( self::$assets[ $handle ]['style'] ) ) {
			wp_register_style(
				$handle,
				self::$assets[ $handle ]['src'],
				self::$assets[ $handle ]['deps'] ?? [],
				self::$assets[ $handle ]['ver'] ?? METCRAFT_VERSION,
				self::$assets[ $handle ]['media'] ?? 'all'
			);
		}
	}

	public static function get_script( $handle ) {
		if ( isset( self::$assets[ $handle ] ) && ! empty( self::$assets[ $handle ]['script'] ) ) {
			wp_register_script(
				$handle,
				self::$assets[ $handle ]['src'],
				self::$assets[ $handle ]['deps'] ?? [],
				self::$assets[ $handle ]['ver'] ?? METCRAFT_VERSION,
				self::$assets[ $handle ]['in_footer'] ?? true
			);
		}
	}

	public static function remove_script( $handle ) {
		wp_deregister_script( $handle );
	}

}
