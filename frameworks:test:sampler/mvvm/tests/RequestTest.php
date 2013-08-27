<?php

namespace jlmvc\testing;

use jlmvc\core\Config;
use jlmvc\core\sys;
use jlmvc\core\Environment;
use jlmvc\core\Request;


class RequestTest extends UnitTestCase
{
	public function __construct() {
		parent::__construct();
	}

	public function test_request( )
	{
		$request = new Request();

		$this->assert->are_equal( '' , $request->controllerName );
		$this->assert->are_equal( array() , $request->controllerParams );
		$this->assert->are_equal( '' , $request->actionName );
		$this->assert->are_equal( array() , $request->actionParams );

	}
}


$test = new RequestTest();
$test->run();

?>