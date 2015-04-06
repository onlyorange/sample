--TEST--
core.posts default
--FILE--
<?php

require_once '../../../../../testLoad.php';
$http = new HTTP_Client();
$http->get('dk.dev.michaelkors.com/api/v1/?json=core.posts&city=mahattan');
$response = $http->currentResponse();
$response = json_decode($response['body']);
$post = $response->posts[0];

echo "Response status: $response->status\n";
echo "Post count: $response->count\n";
echo "City value: $response->city\n";

?>
--EXPECT--
Response status: ok
Post count: 1
City value: Mahattan
