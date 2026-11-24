<?php

namespace MetCraft\Core\Integrations;

defined( 'ABSPATH' ) || exit;

interface Mail_Adapter_Contract {

	public function send( $to, $subject, $message, $headers = [] );

}
