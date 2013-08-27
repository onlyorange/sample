<?php

require dirname(__DIR__) . '/core/Bootstrap.php';

$response = \jlmvc\core\Router::route( 
	new \jlmvc\core\HttpRequest()
);

// if response isn't 200, handle?
if( $response->status['code'] === 404 )
	die('Request returned a 404 with message: ' . $response->status['message'] );

?>