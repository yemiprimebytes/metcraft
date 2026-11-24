<?php

namespace MetCraft\Core\Browser_Data;

defined( 'ABSPATH' ) || exit;

/**
 * Browser Data capture for MetCraft form entries.
 * Captures visitor browser and device information.
 */
class Capture {

	/**
	 * Capture browser data from current request.
	 *
	 * @return array Browser data.
	 */
	public static function capture() {
		$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
		$browser    = self::get_browser( $user_agent );
		$platform   = self::get_platform( $user_agent );
		$mobile     = self::is_mobile( $user_agent );

		return [
			'browser'  => $browser,
			'platform' => $platform,
			'mobile'   => $mobile,
			'user_agent'=> $user_agent,
			'ip'       => sanitize_text_field( $_SERVER['REMOTE_ADDR'] ?? '' ),
		];
	}

	/**
	 * Detect browser from user agent.
	 */
	private static function get_browser( $ua ) {
		if ( strpos( $ua, 'MSIE' ) !== false || strpos( $ua, 'Trident/' ) !== false ) {
			return 'Internet Explorer';
		}
		if ( strpos( $ua, 'Edge/' ) !== false ) {
			return 'Edge';
		}
		if ( strpos( $ua, 'Chrome/') !== false ) {
			return 'Chrome';
		}
		if ( strpos( $ua, 'Safari/') !== false ) {
			return 'Safari';
		}
		if ( strpos( $ua, 'Firefox/') !== false ) {
			return 'Firefox';
		}
		if ( strpos( $ua, 'Opera/') !== false || strpos( $ua, 'OPR/') !== false ) {
			return 'Opera';
		}
		return 'Unknown';
	}

	/**
	 * Detect platform from user agent.
	 */
	private static function get_platform( $ua ) {
		if ( strpos( $ua, 'Windows') !== false ) {
			return 'Windows';
		}
		if ( strpos( $ua, 'Macintosh') !== false ) {
			return 'macOS';
		}
		if ( strpos( $ua, 'Linux') !== false ) {
			return 'Linux';
		}
		if ( strpos( $ua, 'Android') !== false ) {
			return 'Android';
		}
		if ( strpos( $ua, 'iPhone') !== false || strpos( $ua, 'iPad') !== false ) {
			return 'iOS';
		}
		return 'Unknown';
	}

	/**
	 * Detect mobile device.
	 */
	private static function is_mobile( $ua ) {
		return preg_match( '/(android|iphone|ipad|ipod|blackberry|iemobile|opera mobile)/i', $ua ) === 1;
	}

}
