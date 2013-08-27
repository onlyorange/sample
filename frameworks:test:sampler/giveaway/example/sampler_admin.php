<?php
	require_once('_config.php');
	require_once('../GiveawayHelper.php');
	
	// The helper file doesn't handle database connections
	$mysql = mysql_connect(DB_HOST_NAME, DB_USER_NAME, DB_PASSWORD) or die("Could not connect: ".mysql_error());
	mysql_select_db(DB_NAME);
	
	// simply initial a new admin and then render the page
	$admin = new GiveawayAdminHelper(array(
		'db_table' => DB_SAMPLER_TABLE_GROUND
		, 'access_code' => 'admin123'
		, 'admin_page' => 'sampler_admin.php'
		, 'admin_title' => 'Sample Giveaway'
	));
	$admin->render_page();
?>