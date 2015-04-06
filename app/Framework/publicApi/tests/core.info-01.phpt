--TEST--
core.info default
--FILE--
<?php

require_once '../../../../../testLoad.php';
$http = new HTTP_Client();
$http->get('http://localhost/MVC/?json=core.info');
$response = $http->currentResponse();
$response = json_decode($response['body']);

echo "Response status: $response->status\n";
echo "Controllers:\n";
var_dump($response->controllers);

?>
--EXPECT--
Response status: ok
Controllers:
array(4) {
  [0]=>
  string(4) "core"
}
