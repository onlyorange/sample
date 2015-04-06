--TEST--
core.get_posts default
--FILE--
<?php

require_once '../../../../../testLoad.php';
$http = new HTTP_Client();
$http->get('localhost/MVC/?json=core.get_posts');
$response = $http->currentResponse();
$response = json_decode($response['body']);
$post = $response->posts[0];

echo "Response status: $response->status\n";
echo "Post count: $response->count\n";
echo "Post title: $post->title\n";

?>
--EXPECT--
Response status: ok
Post count: 10
Post title: Testing
