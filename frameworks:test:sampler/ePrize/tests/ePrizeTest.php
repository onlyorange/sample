<?php
include('../ePrize.php');

$email = 'zzeppon@hotmail.com';
$code = '645193797205';

$api = new ePrizeApi('gmcr/giveaway', 'gmcr', true);
$api->debug = true;

// make some generic 
$data = array(
	'on_pack_code' => $code
    , 'email' => $email
    , 'confirm_email' => $email
    , 'first_name' => 'juhong'
	, 'last_name' => 'lee'
    , 'address1' => '373 Park Ave South'
    , 'address2' => 'Floor 9'
    , 'city' => 'New York '
    , 'state' => 'NY'
    , 'zip' => '10016'
    , 'age' => 'yes'
);


// let's query a profile to see if it exists
$result = $api->fetch_profile_by_email($email);

if( $result )
{
	echo("<br/><br/>Found profile<br/><br/>");	
}
else
{
	echo("<br/><br/>no profile found.. creating..<br/><br/>");
	
	$result = $api->create_profile( $data );
	$result = $api->fetch_profile_by_email($email);
}

$api->play_game( $result );

?>
