<?php
	
	// includes
	require_once('../../testing/vUnitPhp.php');
	require_once('../GiveawayHelper.php');
	
	// Define our testing constraints
	define("DB_HOST_NAME", "localhost");
	define("DB_USER_NAME", "root");
	define("DB_PASSWORD", "thrasher66");
	define("DB_NAME", "test_giveaway_helper");
	define("DB_SAMPLER_TABLE", "entries");
	define("ENTRY_LIMIT", 1000);
		
	
	class GiveawayHelperTest extends UnitTestCase
	{
		public $mysql; // mysql link		
		public $user_data = array(); // stores out basic user data
		
		function __construct() {
			parent::__construct();
		}
		
		
		public function set_up() {
			// init our basic user data
			$this->user_data = array(
				'email' => 'email@test.com'
				, 'first_name' => 'first'
				, 'last_name' => 'last'
				, 'address' => 'address_1'
				, 'city' => 'city'
				, 'state_province' => 'state_province'
				, 'postal_code' => 'postal_code'
			);
			
			// conncet to db
			$this->mysql = mysql_connect(DB_HOST_NAME, DB_USER_NAME, DB_PASSWORD) or die("Could not connect: ".mysql_error());
			mysql_select_db(DB_NAME);
		}
		
		public function tear_down() {
			mysql_query("DELETE FROM " . DB_SAMPLER_TABLE) or die("Could not delete: ".mysql_error());;
			mysql_close($this->mysql);	
		}
		
		//
		// helper
		private function init_helper()
		{
			return new GiveawayHelper(array('limit' => ENTRY_LIMIT, 'db_table' => DB_SAMPLER_TABLE));
		}
		

		// ----------------------------------------
		#region Test the basic limit funtionality
		// ----------------------------------------

		/**
		 * Ensure entries are allowed when the limit isn't reached
		 */
		public function test_allows_entries()
		{
			$helper = $this->init_helper();
			$this->assert->is_false( $helper->reached_limit() );
		}
		
		/**
		 * ensure entries aren't allowed whn the limit is equal
		 */
		public function test_limits_entries_when_equals_limit()
		{
			$helper = new GiveawayHelper(array('limit' => 0, 'db_table' => DB_SAMPLER_TABLE));
			$this->assert->is_true( $helper->reached_limit() );
		}
		
		/**
		 * ensure entries aren't allowed when the limit is greater
		 */
		public function test_limits_entries_when_over_limit()
		{
			$helper = new GiveawayHelper(array('limit' => -1, 'db_table' => DB_SAMPLER_TABLE));
			$this->assert->is_true( $helper->reached_limit() );
		}
		
		// ----------------------------------------
		#endregion
		// ----------------------------------------
		
		
		
		// ----------------------------------------
		#region Test submitting entries
		// ----------------------------------------
		
		/**
		 * Ensure an entry works
		 */
		public function test_submit_entry()
		{
			$helper = $this->init_helper();
			$this->assert->are_equal( 
				GiveawayHelper::STATUS_ENTERED
				, $helper->enter_giveaway($this->user_data)
			);
		}
		
		/**
		 * Ensure a duplicate entry does not work
		 */
		public function test_submit_duplicate_entry()
		{
			$helper = $this->init_helper();
			$helper->enter_giveaway($this->user_data);

			$this->assert->are_equal( 
				GiveawayHelper::STATUS_ALREADY_ENTERED
				, $helper->enter_giveaway($this->user_data)
			);
		}
		/**
		 * Ensure a secodn distinct entry works
		 */
		public function test_submit_distinct_entry()
		{
			$helper = $this->init_helper();
			$helper->enter_giveaway($this->user_data);
			
			$this->user_data['email'] = "email2@email.com";

			$this->assert->are_equal( 
				GiveawayHelper::STATUS_ENTERED
				, $helper->enter_giveaway($this->user_data)
			);
		}
		
		/**
		 * Ensure a second distinct but duplicate entry does not work
		 */
		public function test_submit_distinct_but_duplicate_entry()
		{
			$helper = $this->init_helper();
			$helper->enter_giveaway($this->user_data);
			
			$this->user_data['email'] = "email2@email.com";
			$helper->enter_giveaway($this->user_data);

			$this->assert->are_equal( 
				GiveawayHelper::STATUS_ALREADY_ENTERED
				, $helper->enter_giveaway($this->user_data)
			);
		}
		/**
		 * Ensure a user can not enter when the limit has been reached
		 */
		public function test_submit_entry_when_limit_reached()
		{
			$helper = new GiveawayHelper(array('limit' => 0, 'db_table' => DB_SAMPLER_TABLE));
			$this->assert->are_equal( 
				GiveawayHelper::STATUS_REACHED_LIMIT
				, $helper->enter_giveaway($this->user_data)
			);
		}
		/**
		 * Ensure a user can not enter when the limit has been reached
		 */
		public function test_submit_entry_when_limit_over()
		{
			$helper = new GiveawayHelper(array('limit' => -1, 'db_table' => DB_SAMPLER_TABLE));
			$this->assert->are_equal( 
				GiveawayHelper::STATUS_REACHED_LIMIT
				, $helper->enter_giveaway($this->user_data)
			);
		}
	}
	
	$test = new GiveawayHelperTest();
	$test->run();
?>