<?php

namespace GSE;

class Cracks
{
	protected static $_instance = null;

	public function __construct()
	{
		add_filter( 'pre_http_request', array($this,'__replace_server_activation_response'), 90, 3 );
	}

	public static function instance()
	{
		if( is_null(self::$_instance) )
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	function __replace_server_activation_response($result, $parsed_args, $url)
	{
		if( substr($url, 0, 33) == 'https://licence.yithemes.com/api/' || substr($url, 0, 41) == 'https://yithemes.com/wc-api/software-api/' )
		{
			$response = array(
				'body' => json_encode(array(
					'code' => 200,
					'message' => 200,
					'success' => true,
					'activated' => true,
					'licence_expires' => time() + 31556926,
					'activation_remaining' => 0,
					'activation_limit' => 1,
					'is_membership' => true,
				)),
			);
			$result = $response;
			return $result;
		}
		if( substr($url, 0, 26) == 'https://www.themehigh.com/' )
		{
			$response = array(
				'response' => array(
					'code' => 200,
				),
				'body' => json_encode(array(
					'success' => true,
					'license' => 'valid',
					'license_status' => 'valid',
					'expires' => 'lifetime',
				)),
			);
			$result = $response;
			return $result;
		}
		if( substr($url, 0, 32) == 'https://envato.itgalaxy.company/' )
		{
			$response = array(
				'response' => array(
					'code' => 200,
				),
				'body' => json_encode(array(
					'status' => 'successCheck',
					'message' => 'Valid license',
				)),
			);
			$result = $response;
			return $result;
		}
		return $result;
	}
}
return true;