<?php
namespace jlmvc\testing;
use jlmvc\core\Router;

require_once 'vUnitPhp.php';

error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);

define('BASE_PATH', '/Users/christopherjl/Sites/vm/jl/');

require_once '../core/Router.php';
require_once '../core/Config.php';


class RoutingTest extends UnitTestCase
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
			BASE_PATH . 'app/controllers/HomeController.php'
			, $meta['path']
		);
	}
	
	public function test_get_controller()
	{
		$controller = Router::getController('Home');
		$expected = new HomeController();
		
		$this->assert->are_objects_equal(
			$expected
			, $controller
		);
	}
}


$test = new RoutingTest();
$test->run();

?>