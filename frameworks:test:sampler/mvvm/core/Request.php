<?php
/**
 * A Basic request object for handling any data
 * 
 */


namespace jlmvc\core;

use jlmvc\core\Environment;

/**
 * Basic request class to use for all different types of requests
 *
 */
class Request
{
	public $controllerName = '';
	public $controllerParams = array(); // not sure what i'm going to use this for!
	
	public $actionName = '';
	public $actionParams = array(); // will handle any additional params after the action (id, etc..)
	
}

/**
 * HttpRequest class for handling standard web requests
 *
 */
class HttpRequest extends Request
{
	/**
	 * Requested uri
	 */		
	public $requestUri = '';
	/**
	 * Request path, without any params
	 */
	public $requestPath = '';
		
	/**
	 * All parameters values that will be available for this request
	 */
	public $params = array();

	/**
	 * Ease of use environment variable
	 * This stores both php/header information as well as local app information
	 *
	 * @var array
	 */
	protected $_env = array();
	
	
	/**
	 * Construct this request by passing in anything set in the get/post fields
	 */
	public function __construct(
		$get = null
		, $post = null
		, $uri = null
		)
	{
		if( is_null($get) )
			$get = &$_GET;
		if( is_null($post) )
			$post = &$_POST;
		if( is_null($uri) )
			$uri = $_SERVER["REQUEST_URI"];
				
		// Let's setup this request
		$this->requestUri = $this->stripUri( $uri ); 
		$this->params = array_merge( $get, $post );
		$this->deriveControllerAndAction( $this->requestUri );
	}
	
	/**
	 * Strips the provided uri of any query string params
	 *
	 * @var uri string
	 * @return string
	 */
	protected function stripUri( $uri )
	{
		// ensure the raw uri is stored
		if( strpos( $uri, '?' ) !== false )
			$uri = substr( $uri, 0, strpos( $uri, '?' ) );
		if( strlen($uri) > 0 && $uri[strlen($uri)-1] === '/' )
			$uri = rtrim( $uri, '/' );
		return $uri;
	}
	
	/**
	 * Derives the basic controller and action from the parts of the provided uri
	 * TODO: Account for custom routes
	 *
	 * @var uri string
	 *
	 */
	public function deriveControllerAndAction( $uri )
	{
		// sanity check to ensure indexes are correct
		if( is_null($uri) )
			$uri = '/';
		if( $uri === '' || $uri[0] !== '/' )
			$uri = "/$uri";

		// filter this down to a controller & action
		$uri_parts = explode( '/', $uri );
		switch( count( $uri_parts ) )
		{
			case 0:
			case 1:
				$this->controllerName = '';
				$this->actionName = '';
				break;
			
			case 2:
				$this->controllerName = $uri_parts[1];
				$this->actionName = '';
				break;
			
			case 3:
				$this->controllerName = $uri_parts[1];
				$this->actionName = $uri_parts[2];
				break;
			
			default:
				$this->controllerName = $uri_parts[1];
				$this->actionName = $uri_parts[2];
				$this->actionParams = array_slice( $uri_parts, 3);
				break;
		}
	}

	/**
	 * TODO: Mimic lithium and provide easy access to the env here?
	 *
	 */
	public function env( $key, $newValue = null )
	{
		if( !is_null( $newValue ) )
			$this->_env[$key] = $newValue;
		else
		{
			$val = array_key_exists($key, $this->_env)
					? $this->_env[$key]
					: getenv( $key );
			
			if( $val === '' || is_null( $val ) || $val == FALSE )
				$val = Environment::get( $key );
			
			return $val;
		}
	}
}

?>