<?php
namespace jlmvc\testing;

use jlmvc\core\Config;
use jlmvc\core\HttpRequest;
use jlmvc\core\Environment;

/**
 * Integration test between an HttpRequest and the Environment
 */
class HttpRequestAndEnviormentTest extends UnitTestCase
{
	public function __construct() {
		parent::__construct();
	}

	public function set_up() {
		Environment::setEnv('testing');
	}


	public function test_env()
	{
		Environment::setEnv('testing');
		Environment::setConfig('testing', array(
			// Pathing
			'base_url' => '//localhost/' // relative reference uri for this site
			, '~' => '/project/folder/' // url path, relative to the the web root
			
			// Facebook settings
			, 'fb_app_id' => ''
			, 'fb_app_secret' => ''
			, 'fb_app_url' => 'https://apps.facebook.com/myapp-local'

		));


		$request = new HttpRequest(null, null, '/');

		// app specfic
		$this->assert->are_equal( 'testing' , Environment::getEnv() );
		$this->assert->are_equal( '/project/folder/' , Environment::get('~') );
		$this->assert->are_equal( '/project/folder/' , $request->env('~') );
		
		$this->assert->are_equal( '//localhost/' , $request->env('base_url') );
	}
}


$test = new HttpRequestAndEnviormentTest();
$test->run();


?>