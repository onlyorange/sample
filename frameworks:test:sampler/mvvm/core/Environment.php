<?php

namespace jlmvc\core;

/**
 * The environment class lets you easily manage multiple environments and deployments
 * Inspired by the ease of use of lithium's Environment configuration
 *
 * TODO: Internally cache env so look-ups are quicker
 *
 */
class Environment
{
	/**
	 * The name of the current environment
	 *
	 * @var string
	 */
	protected static $_current = '';

	/**
	 * A closure for detecting the current environment
	 * By default, the skeleton app ships with a domain-based decetor, but you can provide your
	 * own means of detecting by whichever method you choose
	 *
	 * @var function
	 */
	public static $_detector = null;

	/**
	 * A pre-set list of enviornments --these can be added to at any time
	 *
	 * @var array
	 */
	protected static $_configurations = array();

	#
	#region Env helpers
	#

	/**
	 * Resets all configurations to a default state
	 *
	 */
	public static function reset()
	{
		static::$_current = '';
		static::$_detector = null;
		static::$_configurations = array(
			Config::$localEnv => array()
			, Config::$developmentEnv => array()
			, Config::$uatEnv => array()
			, Config::$productionEnv => array()
			, Config::$testingEnv => array()
		);
	}

	/**
	 * Checks the current environment to see if equals the one we're comparing
	 *
	 * @var string
	 * @return bool
	 *
	 */
	public static function is( $name )
	{
		return static::$_current == $name;
	}	


	#
	#region Dectector
	#

	/**
	 * Sets the auto-detect method for dynamically determining the environment
	 * 
	 * This will be called after the enviornment config is loaded, 
	 * but before any part of the request has loaded
	 *
	 */
	public static function detector( $detect, $request = null )
	{
		if( is_callable( $detect ) ) // is this is a function, then let's set it
			static::$_detector = $detect;
	}

	protected static function _detector()
	{
		return static::$_detector ?: function($request) {
			// TODO in the case of a null request?
			if( is_null( $request ) || !is_object( $request ) )
				return Config::$localEnv;
			
			$local = array('::1', '127.0.0.1', 'localhost');
			
			switch( true )
			{
				case in_array( $request->env('SERVER_ADDR'), $local ):
					return Config::$localEnv;
			}

			return Config::$productionEnv;
		};
	}


	#
	#endregion
	#


	#
	#region Get/set for the current environment
	#	

	/**
	 * Gets the name of the current environment
	 *
	 * @var string
	 * @return string
	 *
	 */
	public static function getEnv( )
	{
		return static::$_current;
	}
	public static function current()
	{
		return static::$_current;	
	}

	/**
	 * Sets a current environment, by name
	 *
	 * @var string
	 * @return array 	will return all config values for this newly set environment
	 *					If an env by that name isn't found, returns an empty array	
	 *
	 */
	public static function setEnv( $name )
	{
		static::$_current = $name;
		
		if( !isset(static::$_configurations[$name]) ) {
			static::$_configurations[$name] = array();
		}

		return static::$_configurations[$name];
	}


	public static function setByRequest( $request )
	{
		static::$_current = static::_detector()->__invoke($request);
	}

	#
	#endregion
	#


	#
	#region Get/set for environmental variables
	#	

	/**
	 * Gets the configuration value for the current environment
	 *
	 * @var string
	 * @return mixed -- will return the value of the $key or null if not found
	 *
	 */
	public static function get( $name )
	{
		if( isset( static::$_configurations[static::$_current][$name] ) )
			return static::$_configurations[static::$_current][$name];
		return null;
	}

	/**
	 * Sets a configuration value for the current environment
	 *
	 * @var string
	 * @return array -- will return newly updated configuration array
	 *
	 */
	public static function set( $key, $value )
	{
		static::$_configurations[static::$_current][$key] = $value;
		
		return static::$_configurations[static::$_current];
	}

	#
	#endregion
	#


	#
	#region Get/Set full configurations
	#
	
	/**
	 * Gets the full configuration array for the given environment
	 *
	 * @var string
	 * @return array -- returns all config settings, or null of the env isn't found
	 */
	public static function getConfig( $name = null )
	{
		if( is_null( $name ) )
			$name = static::$_current;
		
		if( isset( static::$_configurations[$name] ) )
			return static::$_configurations[$name];

		return array();
	}

	/**
	 * Sets the full configuration array for the given environment
	 *
	 * @var string
	 * @var array 
	 *
	 * @return array -- returns the newly updated configuration
	 */
	public static function setConfig( $name, $config = array() )
	{
		static::$_configurations[$name] = $config;
		return static::$_configurations[$name];
	}
	
	#
	#endregion
	#
}

//
// Init our static class be resetting it to the default state as soon as its auto-loaded
Environment::reset();

//
// Nothing should go below the reset call
//
?>