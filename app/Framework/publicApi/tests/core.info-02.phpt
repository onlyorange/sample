--TEST--
core.info controller detail
--FILE--
<?php

require_once 'HTTP/Client.php';
$http = new HTTP_Client();
$http->get('http://localhost/MVC//?json=core.info&controller=core&dev=1');
$response = $http->currentResponse();
$response = json_decode($response['body']);

echo "Response status: $response->status\n";
echo "Name: $response->name\n";
echo "Description: $response->description\n";
echo "Methods:\n";
var_dump($response->methods);

?>
--EXPECT--
Response status: ok
Name: Core
Description: Basic introspection methods
Methods:
array(16) {
  [0]=>
  string(4) "info"
  [1]=>
  string(16) "get_recent_posts"
  [2]=>
  string(9) "get_posts"
  [3]=>
  string(4) "post"
  [4]=>
  string(4) "page"
}
