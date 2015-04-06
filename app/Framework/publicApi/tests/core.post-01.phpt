--TEST--
core.post default
--FILE--
<?php

require_once 'HTTP/Client.php';
$http = new HTTP_Client();
$http->get('http://localhost/MVC/?json=core.post&slug=testing');
$response = $http->currentResponse();
$response = json_decode($response['body']);

echo "Response status: $response->status\n";
echo "post title: {$response->post->title}\n";

?>
--EXPECT--
Response status: ok
post title: Testing
