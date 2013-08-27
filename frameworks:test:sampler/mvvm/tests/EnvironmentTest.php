<?php

namespace jlmvc\testing;

use jlmvc\core\Config;
use jlmvc\core\sys;
use jlmvc\core\Environment;
use jlmvc\core\Request;
use jlmvc\core\HttpRequest;


class EnvironmentTest extends UnitTestCase
{
	public function __construct() {
		parent::__construct();
	}

	public function set_up() {
		Environment::reset();
	}

	#
	#region is functions
	#

	public function test_is_local( )
	{
		Environment::setEnv('local');
		$this->assert->is_true( Environment::is('local') );
	}
	public function test_is_not_local( )
	{
		Environment::setEnv('n/a');
		$this->assert->is_false( Environment::is('local') );
		Environment::setEnv('development');
		$this->assert->is_false( Environment::is('local') );
		Environment::setEnv('uat');
		$this->assert->is_false( Environment::is('local') );
		Environment::setEnv('production');
		$this->assert->is_false( Environment::is('local') );
	}

	public function test_is_development( )
	{
		Environment::setEnv('development');
		$this->assert->is_true( Environment::is('development') );
	}
	public function test_is_not_development( )
	{
		Environment::setEnv('n/a');
		$this->assert->is_false( Environment::is('development') );
		Environment::setEnv('local');
		$this->assert->is_false( Environment::is('development') );
		Environment::setEnv('uat');
		$this->assert->is_false( Environment::is('development') );
		Environment::setEnv('production');
		$this->assert->is_false( Environment::is('development') );
	}

	public function test_is_uat( )
	{
		Environment::setEnv('uat');
		$this->assert->is_true( Environment::is('uat') );
	}
	public function test_is_not_uat( )
	{
		Environment::setEnv('n/a');
		$this->assert->is_false( Environment::is('uat') );
		Environment::setEnv('local');
		$this->assert->is_false( Environment::is('uat') );
		Environment::setEnv('development');
		$this->assert->is_false( Environment::is('uat') );
		Environment::setEnv('production');
		$this->assert->is_false( Environment::is('uat') );
	}

	public function test_is_production( )
	{
		Environment::setEnv('production');
		$this->assert->is_true( Environment::is('production') );
	}
	public function test_is_not_production( )
	{
		Environment::setEnv('n/a');
		$this->assert->is_false( Environment::is('production') );
		Environment::setEnv('local');
		$this->assert->is_false( Environment::is('production') );
		Environment::setEnv('development');
		$this->assert->is_false( Environment::is('production') );
		Environment::setEnv('uat');
		$this->assert->is_false( Environment::is('production') );
	}

	#
	#endregion
	#

	#
	#region Get/Set Env tests
	#

	public function test_set_env()
	{
		Environment::setEnv('test-env');
		$this->assert->is_true( Environment::is('test-env') );
		$this->assert->is_false( Environment::is('testing') );
	}

	public function test_get_env()
	{
		Environment::setEnv('test-env');
		$this->assert->are_equal( 'test-env', Environment::getEnv() );
		$this->assert->are_not_equal( 'testing', Environment::getEnv() );
	}

	public function test_set_by_request( )
	{
		$request = new HttpRequest();
		Environment::setByRequest( $request );
	}

	#
	#endregion
	#

	
	#
	#region Get/Set config tests
	#

	public function test_set_config()
	{
		Environment::setEnv('test-env');

		$config = array(
			'key1' => 'value1'
			, 'key2' => 'value2'
			, 'key3' => 'value3'
			, 'key4' => 'value4'
		);
		
		$result = Environment::setConfig('test-env', $config);

		$this->assert->are_equal( 4, count( $result ) );

		$this->assert->are_equal( 'value1', $result['key1'] );
		$this->assert->are_equal( 'value2', $result['key2'] );
		$this->assert->are_equal( 'value3', $result['key3'] );
		$this->assert->are_equal( 'value4', $result['key4'] );
	}

	public function test_get_config()
	{
		Environment::setEnv('test-env');
		Environment::setConfig('test-env', array(
			'key1' => 'value1'
			, 'key2' => 'value2'
			, 'key3' => 'value3'
			, 'key4' => 'value4'
		));

		$result = Environment::getConfig('test-env');
		
		$this->assert->are_equal( 4, count( $result ) );

		$this->assert->are_equal( 'value1', $result['key1'] );
		$this->assert->are_equal( 'value2', $result['key2'] );
		$this->assert->are_equal( 'value3', $result['key3'] );
		$this->assert->are_equal( 'value4', $result['key4'] );
	}

	public function test_get_config_when_null_name()
	{
		Environment::setEnv('test-env');
		Environment::setConfig('test-env', array(
			'key1' => 'value1'
		));
		
		$result = Environment::getConfig();
		
		$this->assert->are_equal( 1, count( $result ) );
		$this->assert->are_equal( 'value1', $result['key1'] );
	}

	#
	#endregion
	#

	#
	#region Get/Set config value tests
	#

	public function test_get_config_value()
	{
		Environment::setConfig('test-env', array(
			'key1' => 'value1'
			, 'key2' => 'value2'
			, 'key3' => 'value3'
			, 'key4' => 'value4'
		));

		Environment::setEnv('test-env');

		$this->assert->are_equal( 'value1' , Environment::get('key1') );
		$this->assert->are_equal( 'value2' , Environment::get('key2') );
		$this->assert->are_equal( 'value3' , Environment::get('key3') );
		$this->assert->are_equal( 'value4' , Environment::get('key4') );
		$this->assert->are_equal( null , Environment::get('key5') );
	}

	public function test_set_config_value()
	{
		Environment::setEnv('test-env');		
		$this->assert->are_equal( null , Environment::get('key1') );

		Environment::set('key1', 'value1');

		$this->assert->are_equal( 'value1' , Environment::get('key1') );
	}

	

	#
	#endregion
	#


	#
	#region Detector tests
	#

	public function test_set_detector()
	{
		Environment::setEnv('test-env');
		Environment::setConfig('test-env', array(
			'key1' => 'value1'
		));

		try
		{
			Environment::detector( function($request) {
				return true;
			});
			$this->assert->is_true(true);			
		}
		catch( \Exception $e )
		{
			$this->assert->add_failed_result( 
				"Failed to set a function"
				, 'test_set_detector'
			);
		}
	}

	public function test_default_decector()
	{
		$request = new HttpRequest();
		
		// local 
		$request->env('SERVER_ADDR','localhost');
		Environment::setByRequest( $request );
		$this->assert->is_true( Environment::is( Config::$localEnv ) );
		$this->assert->are_equal( 
			Config::$localEnv
			, Environment::current()
		);

		// fall-through
		$request->env('SERVER_ADDR','production');
		Environment::setByRequest( $request );
		$this->assert->is_true( Environment::is('production') );
	}

	public function test_custom_decector()
	{
		Environment::detector( function($request)
		{
			switch( true )
			{
				case $request->controllerName == 'test':
					return Config::$testingEnv;

				case $request->env('HTTP_HOST') == 'localhost':
					return Config::$localEnv;

				case $request->env('HTTP_HOST') == 'development':
					return Config::$developmentEnv;

				case $request->env('HTTP_HOST') == 'uat':
					return Config::$uatEnv;

				case $request->env('HTTP_HOST') == 'production':
					return Config::$productionEnv;
			}
		});

		$request = new HttpRequest();
		$request->env('SERVER_ADDR','localhost');
		Environment::setByRequest( $request );

		$this->assert->are_equal(
			'local'
			, Environment::current()
		);
	}

	#
	#endregion
	#
}

$test = new EnvironmentTest();
$test->run();

?>