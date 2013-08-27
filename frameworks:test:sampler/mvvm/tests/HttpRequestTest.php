<?php
namespace jlmvc\testing;

use jlmvc\core\Config;
use jlmvc\core\HttpRequest;

class HttpRequestTest extends UnitTestCase
{
	public function __construct() {
		parent::__construct();
	}
	
	
	public function test_default_constructor()
	{
		// this should return the request of this testing url
		$request = new HttpRequest();
		
		$this->assert->are_equal( 'tests' , $request->controllerName );
		$this->assert->are_equal( array() , $request->actionParams );
	}	
	
	public function test_empty_request()
	{
		$request = new HttpRequest(null, null, '');
		
		$this->assert->are_equal( '' , $request->requestUri );
		$this->assert->are_equal( '' , $request->controllerName );
		$this->assert->are_equal( '' , $request->actionName );
		$this->assert->are_equal( 0 , count( $request->actionParams ) );
	}
	
	public function test_empty_request_with_query_string()
	{
		$request = new HttpRequest(null, null, '?key=value');
		
		$this->assert->are_equal( '' , $request->requestUri );
		$this->assert->are_equal( '' , $request->controllerName );
		$this->assert->are_equal( '' , $request->actionName );
		$this->assert->are_equal( 0 , count( $request->actionParams ) );
	}
	
	public function test_root_request()
	{
		$request = new HttpRequest(null, null, '/');
		
		$this->assert->are_equal( '' , $request->requestUri );
		$this->assert->are_equal( '' , $request->controllerName );
		$this->assert->are_equal( '' , $request->actionName );
		$this->assert->are_equal( 0 , count( $request->actionParams ) );
	}
	public function test_root_request_with_query_string()
	{
		$request = new HttpRequest(null, null, '/?key=value');
		
		$this->assert->are_equal( '' , $request->requestUri );
		$this->assert->are_equal( '' , $request->controllerName );
		$this->assert->are_equal( '' , $request->actionName );
		$this->assert->are_equal( 0 , count( $request->actionParams ) );
	}
	
	public function test_controller_default_request()
	{
		$request = new HttpRequest(null, null, '/controller');
		
		$this->assert->are_equal( '/controller' , $request->requestUri );
		$this->assert->are_equal( 'controller' , $request->controllerName );
		$this->assert->are_equal( '' , $request->actionName );
		$this->assert->are_equal( 0 , count( $request->actionParams ) );
	}
	
	public function test_controller_index_request()
	{
		$request = new HttpRequest(null, null, '/controller/index');
		
		$this->assert->are_equal( '/controller/index' , $request->requestUri );
		$this->assert->are_equal( 'controller' , $request->controllerName );
		$this->assert->are_equal( 'index' , $request->actionName );
		$this->assert->are_equal( 0 , count( $request->actionParams ) );
	}
	
	public function test_controller_action_request()
	{
		$request = new HttpRequest(null, null, '/controller/action');
		
		$this->assert->are_equal( '/controller/action' , $request->requestUri );
		$this->assert->are_equal( 'controller' , $request->controllerName );
		$this->assert->are_equal( 'action' , $request->actionName );
		$this->assert->are_equal( 0 , count( $request->actionParams ) );
	}
	public function test_controller_action_request_with_trailing_slash()
	{
		$request = new HttpRequest(null, null, '/controller/action/');
		
		$this->assert->are_equal( '/controller/action' , $request->requestUri );
		$this->assert->are_equal( 'controller' , $request->controllerName );
		$this->assert->are_equal( 'action' , $request->actionName );
		$this->assert->are_equal( 0 , count( $request->actionParams ) );
	}
	
	public function test_controller_action_with_one_param_request()
	{
		$request = new HttpRequest(null, null, '/controller/action/one');
		
		$this->assert->are_equal( 'controller' , $request->controllerName );
		$this->assert->are_equal( 'action' , $request->actionName );
		$this->assert->are_equal( 1 , count($request->actionParams) );
		$this->assert->are_equal( 'one' , $request->actionParams[0] );
	}
	public function test_controller_action_with_one_param_request_with_trailing_slash()
	{
		$request = new HttpRequest(null, null, '/controller/action/one/');
		
		$this->assert->are_equal( 'controller' , $request->controllerName );
		$this->assert->are_equal( 'action' , $request->actionName );
		$this->assert->are_equal( 1 , count($request->actionParams) );
		$this->assert->are_equal( 'one' , $request->actionParams[0] );
	}
	
	public function test_controller_action_with_two_params_request()
	{
		$request = new HttpRequest(null, null, '/controller/action/one/two');
		
		$this->assert->are_equal( 'controller' , $request->controllerName );
		$this->assert->are_equal( 'action' , $request->actionName );
		$this->assert->are_equal( 2 , count($request->actionParams) );
		$this->assert->are_equal( 'one' , $request->actionParams[0] );
		$this->assert->are_equal( 'two' , $request->actionParams[1] );
	}
	
	public function test_controller_action_with_two_params_request_with_trailing_slash()
	{
		$request = new HttpRequest(null, null, '/controller/action/one/two/');
		
		$this->assert->are_equal( 'controller' , $request->controllerName );
		$this->assert->are_equal( 'action' , $request->actionName );
		$this->assert->are_equal( 2 , count($request->actionParams) );
		$this->assert->are_equal( 'one' , $request->actionParams[0] );
		$this->assert->are_equal( 'two' , $request->actionParams[1] );
	}


	#
	#region ENV settings
	#

	public function test_env()
	{
		$request = new HttpRequest(null, null, '/');
		
		// some globals
		$this->assert->are_equal( 'localhost' , $request->env('HTTP_HOST') );
		$this->assert->are_equal( '/tests/HttpRequestTest' , $request->env('REQUEST_URI') );
		
		// For Environment integration tests, see HttpRequestAndEnvironmentTest.php

	}

	public function test_env_set( )
	{
		$request = new HttpRequest(null, null, '/');
		$request->env('key1', 'value1');
		$request->env('key2', 'value2');
		
		$this->assert->are_equal(
			'value1'
			, $request->env('key1')
		);
		$this->assert->are_equal(
			'value2'
			, $request->env('key2')
		);
	}

	#
	#endregion
	#
	
}


$test = new HttpRequestTest();
$test->run();

?>