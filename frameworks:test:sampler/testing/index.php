<?php

include("vUnitPhp.php");

class MyTest extends UnitTestCase
{
	public function __construct() {
		parent::__construct();
	}
	
	// you can use these to setup a db, delete all data, etc..
	public function set_up()
	{
	}
	
	// this gets run after every test
	public function tear_down()
	{
	}
	
	//
	// ensure true and false tests work
	public function test_is_true_passed()
	{
		$this->assert->is_true(true);
	}
	
	public function test_is_true_failed()
	{
		$this->assert->is_false(true);
	}
	
	public function test_is_false_passed()
	{
		$this->assert->is_false(false);
	}
	
	public function test_is_false_failed()
	{
		$this->assert->is_true(false);
	}
	
	//
	// Test are_equal cases
	public function test_are_equal_passes_with_bools( )
	{
		$this->assert->are_equal(true, true);
	}
	public function test_are_equal_passes_with_ints( )
	{
		$this->assert->are_equal(10, 10);
	}
	public function test_are_equal_passes_with_floats( )
	{
		$this->assert->are_equal(3.14, 3.14);
	}
	public function test_are_equal_passes_with_strings( )
	{
		$this->assert->are_equal('test', 'test');
	}
	
	public function test_are_equal_fails_with_bools( )
	{
		$this->assert->are_equal(true, false);
	}
	public function test_are_equal_fails_with_ints( )
	{
		$this->assert->are_equal(10, 20);
	}
	public function test_are_equal_fails_with_floats( )
	{
		$this->assert->are_equal(3.14, 3.142);
	}
	public function test_are_equal_fails_with_strings( )
	{
		$this->assert->are_equal('test', 'test2');
	}
	
	//
	// test are_not_equal
	public function test_are_not_equal_passes_with_bools( )
	{
		$this->assert->are_not_equal(true, true);
	}
	public function test_are_not_equal_passes_with_ints( )
	{
		$this->assert->are_not_equal(10, 10);
	}
	public function test_are_not_equal_passes_with_floats( )
	{
		$this->assert->are_not_equal(3.14, 3.14);
	}
	public function test_are_not_equal_passes_with_strings( )
	{
		$this->assert->are_not_equal('test', 'test');
	}

	public function test_are_not_equal_fails_with_bools( )
	{
		$this->assert->are_not_equal(true, false);
	}
	public function test_are_not_equal_fails_with_ints( )
	{
		$this->assert->are_not_equal(10, 20);
	}
	public function test_are_not_equal_fails_with_floats( )
	{
		$this->assert->are_not_equal(3.14, 3.142);
	}
	public function test_are_not_equal_fails_with_strings( )
	{
		$this->assert->are_not_equal('test', 'test2');
	}	
}


$test = new MyTest();
$test->run();

?>