<?php
namespace jlmvc\testing;

use jlmvc\core\Config;
use jlmvc\core\sys;

class ConfigTest extends UnitTestCase
{
	public function __construct() {
		parent::__construct();	}

	public function test_get_model_path()
	{
		$this->assert->are_equal(
			sys::makePath(BASE_PATH, Config::$appPath, Config::$modelPath)
			, Config::getModelPath()
		);
	}
	public function test_get_view_path()
	{
		$this->assert->are_equal(
			sys::makePath(BASE_PATH, Config::$appPath, Config::$viewPath)
			, Config::getViewPath()
		);
	}
	public function test_get_controller_path()
	{
		$this->assert->are_equal(
			sys::makePath(BASE_PATH, Config::$appPath, Config::$controllerPath)
			, Config::getControllerPath()
		);
	}	
}

$test = new ConfigTest();
$test->run();

?>