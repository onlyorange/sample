<?php
namespace jlmvc\core;

use jlmvc\core\Config;
use jlmvc\core\Helper;

class Response
{
	public $status = array( 
		'code' => 200
		, 'message' => 'ok'
	);
	
	public $encoding = 'UTF-8';

	public function __construct( $args = null )
	{
		if( isset( $args['code'] ) )
		{
			$this->status['code'] = $args['code'];
			
			if( isset( $args['message'] ) )
				$this->status['message'] = $args['message'];
			else
				$this->status['message'] = ResponseCodes::$statuses[ $args['code'] ];
		}
	}
}

class ResponseCodes
{
	public static $statuses = array(
		100 => 'Continue',
		101 => 'Switching Protocols',
		102 => 'Processing',
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		307 => 'Temporary Redirect',
		308 => 'Permanent Redirect',
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Time-out',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Large',
		415 => 'Unsupported Media Type',
		416 => 'Requested Range Not Satisfiable',
		417 => 'Expectation Failed',
		422 => 'Unprocessable Entity',
		423 => 'Locked',
		424 => 'Method Failure',
		428 => 'Precondition Required',
		451 => 'Unavailable For Legal Reasons',
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Time-out',
		507 => 'Insufficient Storage'
	);
}

?>