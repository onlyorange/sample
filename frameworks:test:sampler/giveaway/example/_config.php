<?php
	
	//
	// DATABASE INFO
	define("DB_HOST_NAME", "localhost");
	define("DB_USER_NAME", "db_user");
	define("DB_PASSWORD", "db_password");
	define("DB_NAME", "project_name");
	define("DB_SAMPLER_TABLE", "entries");
	
	//
	// GIVEAWAY RELATED
	define("ENTRY_LIMIT", 1000);
	
	//
	// ease of use function for connecting to the database
	function db_connect() {
		$mysql = mysql_connect(DB_HOST_NAME, DB_USER_NAME, DB_PASSWORD) or die("Could not connect: ".mysql_error());
		mysql_select_db(DB_NAME);	
	}

?>