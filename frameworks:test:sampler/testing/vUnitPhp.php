<?php
	//
	//
	// Super basic unit testing suite
	//
	//
	
	/**
	 * Basic result message
	 */
	 
	abstract class TestResult
	{
		public $test_name;		
		public $timestamp;
		public $status;
		public $message;
		
		// some constants for the result types
		const TYPE_PASSED = 'passed';
		const TYPE_FAILED = 'failed';
		const TYPE_ERRORED = 'errored';
		
		
		function __construct( $test_name, $status, $message = '' )
		{
			list(
				$this->timestamp
				, $this->test_name
				, $this->status
				, $this->message
			) = array(
				time()
				, $test_name
				, $status
				, $message
			);
		}
			
	}
	
	/**
	 * Captures a passed test result
	 **/
	class PassedResult extends TestResult {
		function __construct( $test_name, $message = '' ) {
			parent::__construct( $test_name, TestResult::TYPE_PASSED, $message );
		}
	}
	/**
	 * Captures a failed test result
	 **/
	class FailedResult extends TestResult {
		function __construct( $test_name, $message = '' ) {
			parent::__construct( $test_name, TestResult::TYPE_FAILED, $message );
		}
	}
	/**
	 * Captures an error test result
	 **/
	class ErrorResult extends TestResult {
		function __construct( $test_name, $message = '' ) {
			parent::__construct( $test_name, TestResult::TYPE_ERRORED, $message );
		}
	}
	
	/**
	 * Captures the output report for a particular set of tests
	 */
	class TestReporter
	{
		/**
		 * Keeps track of all of our results
		 */
		public $results;
		
		function __construct( )
		{
			$this->results = array();
		}
		
		/**
		 * Adds a result to our report
		 */
		public function add_result( $result ) {
			array_push( $this->results, $result );
		}
		
		// TODO: Look for a template file to use if there is one
		public function render_html( ) {
			echo(	"<table style='width: 800px; border: solid 1px black;' border='1'>"
						. "<tr>"
							. "<th width='400' style='text-align:left;'>Test</th>"
							. "<th width='50' style='text-align:left;'>Status</th>"
							. "<th style='text-align:left;'>Message</th>"
						. "<tr>"
			);
			
			foreach( $this->results as $result )
			{
				$status = '';
				$color = '';
				
				if( $result->status == TestResult::TYPE_PASSED ){
					$status = 'PASSED';
					$color = 'green';
				} else {
					$status = 'FAILED';
					$color = 'red';
				}
				
				echo( 	"<tr>"
							. "<td>$result->test_name</td>"
							. "<td style='background-color: $color'>$status</td>"
							. "<td>$result->message</td>"
						. "</tr>"
				);
			}
			
			echo("</table>");
		}
	}
	
	
	/**
	 * 	Basic class for handling any type of test case. Handles some basic reflection, interface, etc..
	 * 
	 * 
	 */
	
	abstract class TestCase
	{
		public $test;
		/**
		 * An instance of a reporter to track all of our results
		 */
		public $reporter; 
		
		/**
		 * The prefix to use when determining if something is a test (defaults to 'test')
		 */
		private $test_prefix = 'test';
		/**
		 * The display name for this test 
		 */	
		protected $test_name;
		
		
		/**
		 * Basic constructor; sets up the test display name from either the current class, or a pre-defined label
		 * 
		 */
		function __construct()
		{
			$this->test_name = get_class($this);
			$this->reporter = new TestReporter();
	    }
		
		/**
		 * Function that runs all the unit tests
		 * 
		 */
		 
		 public function run()
		 {
		 	$tests = $this->get_tests();
			foreach( $tests as $method ) {
				$this->set_up();
				$this->$method();
				$this->tear_down();
			}
			
			$this->reporter->render_html();
		 } 
		
		
		// --------------------------------------------
		#region reflection helpers
		// --------------------------------------------
		
		/**
		 * Gets all the test methods defined in this class
		 * 
		 * @return array - an array of all test methods
		 */
		public function get_tests()
		{
	        $methods = array();
	        foreach (get_class_methods(get_class($this)) as $method)
	        {
	            if ($this->is_test($method))
	                array_push($methods, $method);
	        }
	        return $methods;
	    }
		
		/**
		 * Determines if a method is a test by checking the preface
		 * 
		 * @return boolean - true or false if this particular method is to be considered a test method
		 */
		public function is_test($method)
		{
			if (strtolower(substr($method, 0, 4)) === $this->test_prefix)
            	return true; 
        	return false;
		}
		
		// --------------------------------------------
		#endregion
		// --------------------------------------------
				
		// --------------------------------------------
		#region stumbs to override in the instance class
		// --------------------------------------------
		
		/**
	     * Used to setup a test. Should be override in the implementation
		 * 
	     */
	    function set_up() { }
	
	    /**
	     * Used to remove any data after a particular test case. Should be override in the implemtation
	     */
	    function tear_down() { }
		
		// --------------------------------------------
		#endregion
		// --------------------------------------------
			
		
	}
	
	
	/**
	 * Provides an interface for creating basic unit tests 
	 * 
	 * 
	 */
	
	class UnitTestCase extends TestCase
	{
		public $assert;
		public $results = array();
		
		public function __construct()
		{
			parent::__construct();
			$this->assert = new Assert($this->reporter);
		}
	}
	
	/**
	 * Allows us to easily assert different test conditions for reporting and logging
	 * 
	 * 
	 */
	class Assert
	{
		protected $reporter;
		
		/**
		 * Sets up a new set of assert methods
		 * 
		 * @param reporter - the TestReporter instance we should use to catalog results
		 * 
		 */
		public function __construct($reporter)
		{
			$this->reporter = $reporter;
		}
		
		/**
		 * Gets teh name of the current test method being run
		 */
		private function get_test_name( $depth = 0)
		{
			$callstack = debug_backtrace();
			return $callstack[2+$depth]['function'];
		}
		
		private function add_passed_result( $message = '' ) {
			$this->reporter->add_result( new PassedResult( $this->get_test_name(1), $message ) ); 
		}
		
		private function add_failed_result( $message = '' ) {
			$this->reporter->add_result( new FailedResult( $this->get_test_name(1), $message ) ); 
		}
		
		// ----------------------------------
		// Basic true/false testing
		// ----------------------------------
		
		/**
		 * Checks to see if the value is true
		 * 
		 * @param actual - the value you did receive
		 * 
		 * @return void (will echo out the result)
		 */
		public function is_true( $actual ) {
				
			if( $actual === true )
				$this->add_passed_result( );
			else
				$this->add_failed_result( "expected: 'true' -- actual: '$actual'" );
		}
		
		/**
		 * Checks to see if the value is true
		 * 
		 * @param actual 		- the value you did receive
		 * @param test_name		- the name of this test (for reporting)
		 * 
		 * @return void (will echo out the result)
		 * 
		 */
		public function is_false( $actual ) {
			if( $actual === false )
				$this->add_passed_result( );
			else
				$this->add_failed_result( "expected: 'false' -- actual: '$actual'");
		}
		
		// ----------------------------------
		// Basic comparison testing
		// ----------------------------------
		
		/**
		 * Checks to see if the expected value is equal to (in both type & value) to the actual value
		 * 
		 * @param expected 		- the value you expect to receive
		 * @param actual 		- the value you did receive
		 * 
		 * @return void (will echo out the result)
		 * 
		 */
		public function are_equal( $expected, $actual ) {
			if( $expected === $actual )
				$this->add_passed_result( );
			else
				$this->add_failed_result( "expected: '$expected' -- actual: '$actual'");
		}
		
		/**
		 * Checks to see if the expected value is not equal to (in both type & value) to the actual value
		 * 
		 * @param expected 		- the value you expect to receive
		 * @param actual 		- the value you did receive
		 * 
		 * @return void (will echo out the result)
		 * 
		 */
		public function are_not_equal($expected, $actual ) {
			if( $expected !== $actual )
				$this->add_passed_result( );
			else
				$this->add_failed_result( "expected: '$expected' -- actual: '$actual'");
		}		
	}

?>