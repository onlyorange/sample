<?php

class ePrizeApi {

    public $key = '';
    public $url_key = '';
	public $url = '';
    public $event_id = '';
	public $debug = false;
	public $is_review = false;
	
	public $REVIEW_URL = 'review.api.eprize.com/v1/';
	public $PROMO_URL = 'promo.api.eprize.com/v1/';

    public function __construct( $key, $url_key, $is_review = false)
    {
        $this->url_key = $url_key;
		$this->key = $key;
		$this->is_review = $is_review;
		
		if( $this->is_review )
			$this->url = $this->REVIEW_URL;
		else
			$this->url = $this->PROMO_URL;
		
		// this constructs our final url for use in all API calls
		$this->url = 'http://' . $url_key . '.' . $this->url . $this->key;
    }
	
	
	// ----------------------------------
	// Init basic get/curl posts
	// ----------------------------------
	
	
	/**
	 * Initiates a curl request via POST
	 */
    function init_curl_post( $url, $data ) {
        $data_string = http_build_query( $data );

		if( $this->debug ) {
	        echo('init post<br/>');
	        echo("url: $url<br/>");
	        echo("data: $data_string<br/><br/>");
		}

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept: application/json'
        ));
        return $ch;
    }
	
	/**
	 * Initiates a curl request via GET
	 */
    function init_curl_get( $url ) {
       
	   if( $this->debug ) {
	        echo('init get<br/>');
	        echo("url: $url<br/><br/>");
		}
	   
	    $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept: application/json'
        ));
        return $ch;
    }
	
	
	// ----------------------------------
	// Profile Related
	// ----------------------------------
	

	/**
	 * Returns a user profile by email address
	 * @param email -- the email address used to create this profile
	 * 
	 */
    function fetch_profile_by_email( $email ) {
        $ch = $this->init_curl_get($this->url . "/profile(email)/$email");
        $result = curl_exec($ch);

		if( $this->debug )
        	var_dump( curl_getinfo($ch) );

        list($header, $body) = explode("\r\n\r\n", $result, 2);
        $parts = explode("\n", $header);
		
		//if( $this->debug )
        	//var_dump($parts);

        if( isset($parts[0]) && trim($parts[0]) == 'HTTP/1.1 303 See Other' ) {
            $location = explode(': ', $parts[3]);
            return trim($location[1]);
        }
        return false;
    }
	
	/**
	 * Creates an ePrize profile
	 *   
	 * @param data array - an array of the profile data to send directly to ePrize 
	 */
    function create_profile( $data ) {
        $ch = $this->init_curl_post($this->url . "/profiles", $data);
        $result = curl_exec($ch);
		
		if( $this->debug ){
			echo("create profile result:<br/>");
			var_dump( curl_getinfo($ch) );
			echo("<br/><br/>");
			var_dump($result);
			echo("<br/><br/>");
		}
        
        //$this->register_for_sweeps($this->fetch_profile( $data['email'] ));


        curl_close( $ch );
    }
	
	
	// ----------------------------------
	// Games
	// ----------------------------------
	
	/**
	 * Play a game on ePrize
	 * 
	 */
	function play_game( $eprize_profile_id )
	{
		$ch = $this->init_curl_post($this->url . "/play_game", array(
            'profile' => $eprize_profile_id
        ));		 

		$result = json_decode(curl_exec($ch))->result;
		
		if( $this->debug ) {
        	//var_dump( curl_getinfo($ch) );
			echo("<br/><br/>PLAY GAME RESULT:</br>");
			var_dump($result);
			echo("<br/><br/>");
		}
		
		$prize = array();
		
		if( isset($result->limited) ) {
			$prize['status'] = 'limited';
			
			if( $this->debug )
				echo('You\'ve already submitted a code.');
		}
		else if( $result->status == 1 )
		{
			if( $this->debug )
			{
				echo('you won a prize!: ');
				echo( $result->prize->prizeDescription );
			}
			
			$prize['status'] = 'prize';
			$prize['prizeDescription'] = $result->prize->prizeDescription;
			$prize['prizeUuid'] = $result->prize->prizeUuid;			
		}
		else if( $result->status == 0 )
		{
			$prize['status'] = 'coupon';
			$prize['prizeDescription'] = 'Coupon';
			$prize['prizeUuid'] = '';
			
			if( $this->debug )
				echo('you won a coupon!');
		}	
		if( $this->debug )
			var_dump($prize);
		
		return $prize;
	}
	
	
	
	// ----------------------------------
	// Sweepstakes
	// ----------------------------------
	
	/**
	 * Registers a user (by their ePrize_id) for a particular sweepstakes
	 *   
	 * @param data array - an array of the profile data to send directly to ePrize 
	 */
    function register_for_sweeps( $eprize_profile_id )
    {
        $ch = $this->init_curl_post($this->url . "/game/$this->event_id/sweeps", array(
            'profile' => $eprize_profile_id
            , 'event' => 'api-login'
        ));

        $result = curl_exec($ch);
		
		if( $this->debug )
        	var_dump($result);

        curl_close($ch);
        return $result;
    }
}

