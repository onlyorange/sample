<?php
namespace jlmvc\testing;

use jlmvc\core\Config;
use jlmvc\core\Request;
use jlmvc\core\Router;
use jlmvc\core\sys;

class RouterTest extends UnitTestCase
{
	public function __construct() {
		parent::__construct();
	}
	
	
	public function test_get_controller_name()
	{
		$meta = Router::getControllerInfo('Home');
		
		$this->assert->are_equal(
			'Home'
			, $meta['name']
		);
	}
	
	public function test_get_controller_class()
	{
		$meta = Router::getControllerInfo('Home');
		
		$this->assert->are_equal(
			'HomeController'
			, $meta['className']
		);
	}
	
	public function test_get_controller_path()
	{
		$meta = Router::getControllerInfo('Home');
		
		$this->assert->are_equal(
			sys::makePath(BASE_PATH, 'app', 'controllers') . 'HomeController.php'
			, $meta['path']
		);
	}
	
	public function test_get_controller()
	{
		$controller = Router::getController('Home', true);
		
		$this->assert->are_equal(
			Config::$appPath .'\\'. Config::$controllerPath .'\\'. 'HomeController'
			, get_class($controller)
		);
	}

	public function test_get_controller2()
	{
		$controller = Router::getController('Home', true);
		
		$this->assert->are_equal(
			Config::$appPath .'\\'. Config::$controllerPath .'\\'. 'HomeController'
			, get_class($controller)
		);
	}
}


$test = new RouterTest();
$test->run();

?>