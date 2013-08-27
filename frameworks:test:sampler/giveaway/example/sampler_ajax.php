<?php
	require_once('_config.php');
	require_once('../GiveawayHelper.php');
	
	$field_prefix = 'sampler_'; // ease of use for appending a prefix to any of these fields
	
	$user_data = array(
		 'email_address' => $_POST[$field_prefix.'email_address']
		 , 'first_name' => $_POST[$field_prefix.'first_name']
		 , 'last_name' => $_POST[$field_prefix.'last_name']
		 , 'address' => $_POST[$field_prefix.'address']
		 , 'city' => $_POST[$field_prefix.'city']
		 , 'state_province' => $_POST[$field_prefix.'state']
		 , 'postal_code' => $_POST[$field_prefix.'zip']
		 , 'dob' => $_POST[$field_prefix.'day']." ".$_POST[$field_prefix.'month']." ".$_POST[$field_prefix.'year']
	);
	
	// create a new helper for this sample giveaway
	$helper = new GiveawayHelper(array(
		'limit' =>  ENTRY_LIMIT
		, 'db_table' => DB_SAMPLER_TABLE
		, 'user_key' => 'email_address'
	));
	
	//
	// TODO: Validation on user_data
	//
	
	db_connect();
	
	if(true /*Some check to ensure this is a valid post */ ) {
	
		if($helper->already_entered($user_data['email_address']))
			die(GiveawayHelper::STATUS_ALREADY_ENTERED);
		else
			die( $helper->enter_giveaway($user_data) );
			
	} else
		die("need moar coffee");
?>