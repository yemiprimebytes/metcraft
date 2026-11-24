<?php

namespace MetCraft\Core\Integrations;

defined( 'ABSPATH' ) || exit;

class Mail_Adapter {

	public static function send( $to, $subject, $message, $headers = [] ) {
		return wp_mail( $to, $subject, $message, $headers );
	}

}
