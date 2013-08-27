<?php

//
// A suite of helper functions for facilitating a giveaway sample tour
// This assumes you've already setup a connection to the database
//

/*
 -- sample schema
 CREATE TABLE `entries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_entered` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `first_name` varchar(128) NOT NULL,
  `last_name` varchar(128) NOT NULL,
  `address` varchar(128) DEFAULT NULL,
  `city` varchar(128) DEFAULT NULL,
  `state_province` varchar(128) DEFAULT NULL,
  `postal_code` varchar(128) DEFAULT NULL,
  `country` varchar(128) DEFAULT NULL,
  `email` varchar(254) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_address` (`email`)
);
 */ 

/**
 * A basic helper class for any type of giveaway
 * 
 */
class GiveawayHelper {
		
	// define some class level constants -- can access via GiveawayHelper::CONSTANT_NAME
	const KEY_EMAIL = 'email';
	const KEY_FACEBOOK = 'facebook_id';
	
	// statuses for after an entry attempt
	const STATUS_REACHED_LIMIT = 'reached_limit';
	const STATUS_ALREADY_ENTERED = 'already_entered';
	const STATUS_ENTERED = 'ok';
	
	public $limit = 0; // The limit for the number of entries into this giveaway
	public $allow_duplicates = false;
	public $user_key = GiveawayHelper::KEY_EMAIL;
	
	public $db_table = '';
	
	/**
	 * Creates a new GiveawayHelper
	 * @param $args array -- an array of params to initiate this helper 
	 */
	public function __construct( $args )
	{
		if( !isset($args['limit'] ) )
			throw new Exception( "ERROR: no giveaway limit specified" );
		if( !isset($args['db_table'] ) )
			throw new Exception( "ERROR: no database table specified" );
		
		$this->limit = $args['limit'];
		$this->db_table = $args['db_table'];
		
		if( isset($args['allow_duplicates']) )
			$this->allow_duplicates = $args['allow_duplicates'];
		if( isset($args['user_key']) )
			$this->user_key = $args['user_key'];
	}
	
	// ------------------------------------------------------
	// Attempt to enter
	// ------------------------------------------------------
	
	/**
	 * Handles inserting all the user data into the database
	 * please ALWAYS use a timestamp on these >.>
	 * 
	 * @param $user_data -- key => value array
	 * 
	 * @return returns an string status for this entry attempt
	 *
	 */
	public function enter_giveaway( $user_data ) {
			
		if( $this->reached_limit( ) )
			return GiveawayHelper::STATUS_REACHED_LIMIT;
		
		if( $this->already_entered( $user_data[$this->user_key] ) )
			return GiveawayHelper::STATUS_ALREADY_ENTERED;
		
		$field_titles = array();
		$field_values = array();
		
		// loop through all entries and create the insert string for this data
		foreach ($user_data as $key => $value)
		{
			array_push($field_titles, $key);
			array_push($field_values, "'" . $this->mysql_query_string($value) . "'" );
		}
		
		$query = 'insert into ' . $this->db_table 
			. ' (' . implode(',',$field_titles) . ')'
			. ' values (' . implode(',',$field_values) . ')';
		
		//echo("<br/><br/>$query<br/><br/>");
		
		$success = mysql_query($query) or die(mysql_error());
		
		return GiveawayHelper::STATUS_ENTERED;
	}
	
	
	
	// ------------------------------------------------------
	// Helpers for determining if we're able to enter
	// ------------------------------------------------------
	
	
	/**
	 * Determines if this giveaway has reached its total limit 
	 */
	public function reached_limit( )
	{
		$total_query = mysql_query("SELECT COUNT(*) as total FROM $this->db_table");
		if( !$total_query ) // if there's no results then we're not over the limit
			return false;
		
		$trow = mysql_fetch_object($total_query);
		$total = $trow->total;
		
		mysql_free_result($total_query);
	
		if( $total >= $this->limit )
			return true;
		return false;
	}
	
	/**
	 * Checks to see if this user has already entered this contest
	 * @param user_key string -- the key for this user, e.g.. email, facebook_id, etc..
	 * 
	 */
	public function already_entered( $user_key )
	{
		$existing = false;
		
		$result = mysql_query("SELECT * FROM " . $this->db_table . " WHERE " . $this->user_key . " = '$user_key'");
		if ( $result && mysql_num_rows($result) > 0 )
			$existing = true;
		
		//mysql_free_result($result);
		
		return $existing;
	}
	
	
	
	
	
	// ------------------------------------------------------
	// Database helpers
	// ------------------------------------------------------
	
	
	/**
	 * Database helper for safely parsing a value to prevent sql injection
	 * 
	 */
	public function mysql_query_string($string)
	{
		if(get_magic_quotes_gpc()){ return $string; }
		$enabled = true;
		$htmlspecialchars = false; # Convert special characters to HTML entities 
		/****************************************************************
		The translations performed are: 
	
		'&' (ampersand) becomes '&amp;' 
		'"' (double quote) becomes '&quot;' when ENT_NOQUOTES is not set. 
		''' (single quote) becomes '&#039;' only when ENT_QUOTES is set. 
		'<' (less than) becomes '&lt;' 
		'>' (greater than) becomes '&gt;' 
	
		*****************************************************************/
		
		if($htmlspecialchars)
		{
			# Convert special characters to HTML entities 
			$string = htmlspecialchars($string, ENT_QUOTES);
		}
		else
		{
			/****************************************************************
			'"' (double quote) becomes '&quot;' 
			''' (single quote) becomes '&#039;' 
			****************************************************************/
			//$string = str_replace('"',"&quot;",$string);
			//$string = str_replace("'","&#039;",$string);
		}
		if($enabled and gettype($string) == "string")
		{
			# Escapes special characters in a string for use in a SQL statement
			return mysql_real_escape_string(trim($string));
		}
		elseif($enabled and gettype($string) == "array")
		{
			$ary_to_return = array();
			foreach($string as $str)
			{
					$ary_to_return[]=mysql_real_escape_string(trim($str));
			}
			return $ary_to_return;
		}
		else
		{
			return trim($string);
		}
	}	
}


/**
 * Helper for display a super basic admin screen for a sample giveaway
 * 
 */
class GiveawayAdminHelper {
	
	public $db_table = '';
	public $date_entered_field = 'date_entered';
	
	public $access_code = '';
	public $admin_page = 'admin.php';
	public $admin_title = 'Giveaway';
	
	public function __construct( $args )
	{
		$this->db_table = $args['db_table'];
		$this->access_code = $args['access_code'];
		
		if( isset($args['admin_page']) )
			$this->admin_page = $args['admin_page'];
		if( isset($args['admin_title']) )
			$this->admin_title = $args['admin_title'];
		if( isset($args['date_entered']) )
			$this->admin_title = $args['date_entered'];
	}
	
	// ------------------------------------------------------
	// Render functions for various parts of the page
	// ------------------------------------------------------
	
	
	public function render_page() {
			
		$this->render_header();
			
		if( !$this->ensure_login( ) ) {
			$this->render_login();
		}
		else if( isset($_GET['export']) == 1 ) {
			$this->render_export();
		} else {
			$this->render_body();
		}
		
		$this->render_footer();
	}
	
	public function render_body() {
	// proceed to render page
		
		
		if( isset($_COOKIE['last_last_visit']) ) {
			?><h5>Your last visit: <?=$_COOKIE['last_last_visit']?></h5><?php
		}
		
		$total_rows = 0;
		$num_rows = 0;	
		$where = '';
		$display_count = 0;
		
		// get the total # of records
		$total_query = mysql_query("SELECT count(id) as total FROM $this->db_table");
		if( $total_query ) {
			$trow = mysql_fetch_object($total_query);
			$total_rows = $trow->total;
		}

		$list_result = mysql_query("SELECT * FROM $this->db_table $where ORDER BY $this->date_entered_field DESC LIMIT 200 ") or die(mysql_error());
		if( $list_result ) {
			$num_rows = mysql_num_rows($list_result);
			$fields_cnt = mysql_num_fields($list_result);
		} 
		
		?>
		<h3><?=$total_rows?> total entries. (Show latest <?=$num_rows?> entries)</h3>
		<h3><a href="<?php echo $this->admin_page; ?>?export=1">Download all results</a></h3>
		<?php	
		
		if (!$list_result || $num_rows == 0) { ?>
		
			<h4>No results since your last login :(</h4>
		
		<?php } else { ?>
		
			<table>
				<thead>
				<tr>
					<? for ($j = 0; $j < $fields_cnt; $j++) { ?>
						<th><?=mysql_field_name($list_result, $j)?></th>
					<? } ?>
				</tr>
				</thead>
				<? while ($row = mysql_fetch_array($list_result) ) { ?> 
					<tr>
					<? for ($j = 0; $j < $fields_cnt; $j++) { ?>
						<td><?=$row[$j]?></td>
					<? } ?>
					</tr>
				<? } ?>
			</table>
		<?php }
	}
	
	
	
	/**
	 * Renders out the html for a login form
	 * 
	 */
	public function render_login() {
	
		?>
		<form method="post">
		<table>
			<tr>
				<td>Access Code:</td>
				<td><input type="password" id="access_code" name="access_code"/></td>
			</tr>
			<tr>
				<td></td>
				<td><input type="submit" value="submit"/></td>
			</tr>
		</table>
		</form>
		<?php
	}
	
	public function render_export( ) {
		$filename = $this->db_table . '-' . date('m-d-y') . '.csv';
	    $csv_terminated = "\n";
	    $csv_separator = ",";
	    $csv_enclosed = '"';
	    $csv_escaped = "\\";
	    $sql_query =  "SELECT * FROM $this->db_table ORDER BY $this->date_entered_field DESC";
	 
	    // Gets the data from the database
	    $result = mysql_query($sql_query);
	    $fields_cnt = mysql_num_fields($result);
	 
	    $schema_insert = '';
	 
	    for ($i = 0; $i < $fields_cnt; $i++)
	    {
	        $l = $csv_enclosed . str_replace($csv_enclosed, $csv_escaped . $csv_enclosed,
	            stripslashes(mysql_field_name($result, $i))) . $csv_enclosed;
	        $schema_insert .= $l;
	        $schema_insert .= $csv_separator;
	    } // end for
	 
	    $out = trim(substr($schema_insert, 0, -1));
	    $out .= $csv_terminated;
	 
	    // Format the data
	    while ($row = mysql_fetch_array($result))
	    {
	        $schema_insert = '';
	        for ($j = 0; $j < $fields_cnt; $j++)
	        {
	            if ($row[$j] == '0' || $row[$j] != '')
	            {
	 
	                if ($csv_enclosed == '')
	                {
	                    $schema_insert .= $row[$j];
	                } else
	                {
	                    $schema_insert .= $csv_enclosed .
	                    str_replace($csv_enclosed, $csv_escaped . $csv_enclosed, $row[$j]) . $csv_enclosed;
	                }
	            } else
	            {
	                $schema_insert .= '';
	            }
	 
	            if ($j < $fields_cnt - 1)
	            {
	                $schema_insert .= $csv_separator;
	            }
	        } // end for
	 
	        $out .= $schema_insert;
	        $out .= $csv_terminated;
	    } // end while
	 
	    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	    header("Content-Length: " . strlen($out));
	    header("Content-Disposition: attachment; filename=$filename");
	    echo $out;
	    exit;
	}
	
	// ------------------------------------------------------
	// Login/Access validation helpers
	// ------------------------------------------------------
	
	public function ensure_login() 
	{
		if( !isset($this->access_code) ) {
			throw new Exception("ERROR: Login credentials not set --no access allowed");
			return false;
		}
		
		if( isset($_POST['access_code']) && $_POST['access_code'] == $this->access_code ) {
			setcookie("admin_valid", 'valid', time()+3600);
			header( "Location: $this->admin_page" );
		}
		else if( isset($_COOKIE["admin_valid"]) && $_COOKIE["admin_valid"] == 'valid' )
		{
			setcookie('last_last_visit', $_COOKIE['last_visit'], time()+3600);
			setcookie('last_visit', date('Y-m-d H:i:s'), time()+3600 );
			return true;
		}
		
		return false;
	}
	
	// ------------------------------------------------------
	// Render helpers
	// ------------------------------------------------------
	
	public function render_header()
	{
		?>
		<html>
		<head>
			<title><?php echo($this->admin_title); ?> Entry Results</title>
			<style>
				body { font-size: 14px; font-family: "Lucida Sans"; font-weight: normal; }
				table { width: 95%; font: 85% "Lucida Grande", "Lucida Sans Unicode", "Trebuchet MS", sans-serif;padding: 0; margin: 0; border-collapse: collapse; color: #333; background: #F3F5F7;}
				table a {color: #3A4856; text-decoration: none; border-bottom: 1px solid #C6C8CB;}  
				table a:visited {color: #777;}
				table a:hover {color: #000;}  
				table caption {text-align: left; text-transform: uppercase;  padding-bottom: 10px; font: 200% "Lucida Grande", "Lucida Sans Unicode", "Trebuchet MS", sans-serif;}
				table thead th {background: #3A4856; padding: 15px 10px; color: #fff; text-align: left; font-weight: normal;}
				table tbody, table thead {border-left: 1px solid #EAECEE; border-right: 1px solid #EAECEE;}
				table tbody {border-bottom: 1px solid #EAECEE;}
				table tbody td, table tbody th {padding: 10px; background: url("td_back.gif") repeat-x; text-align: left;}
				table tbody tr {background: #F3F5F7;}
				table tbody tr.odd {background: #F0F2F4;}
				table tbody  tr:hover {background: #EAECEE; color: #111;}
				table tfoot td, table tfoot th, table tfoot tr {text-align: left; font: 120%  "Lucida Grande", "Lucida Sans Unicode", "Trebuchet MS", sans-serif; text-transform: uppercase; background: #fff; padding: 10px;}
			</style>
		</head>
		<body>
		<h1><?php echo($this->admin_title); ?> Entry Results</h1>
		<?php
	}
	
	public function render_footer()
	{
		?>
		</body>
		</html>
		<?php		
	}
}
?>