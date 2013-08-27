<?php

/**
 * Environmental config
 * Here you can configure all the different environments available to this project
 */

use jlmvc\core\Environment;
use jlmvc\core\Config;

/**
 * Dynamically set the function that detects which env we're in
 * If left blank, then a default detector will be used
 *
 */
Environment::detector( function( $request ) {

	switch( true )
	{
		case $request->controllerName == 'test':
			return Config::$testingEnv;

		case $request->env('HTTP_HOST') == 'localhost':
			return Config::$localEnv;

		case $request->env('HTTP_HOST') == 'development':
			return Config::$developmentEnv;

		case $request->env('HTTP_HOST') == 'uat':
			return Config::$uatEnv;

		case $request->env('HTTP_HOST') == 'production':
			return Config::$productionEnv;
	}

});






/**
 * Use this to set any global environmental constants
 * @var array
 */
$globalConfig = array(
	'title' => 'My Mvvm App'
	, 'share_copy' => 'This is some share copy to use throughout the app'
	, 'share_image' => 'http://some/path/to/an/image'
);


/**
 * Init our different envs
 */

// Local
Environment::setConfig('local', array_merge( $globalConfig, array(
	// Pathing
	'base_url' => '//localhost/' // relative reference uri for this site
	, '~' => '/project/folder' // url path, relative to the the web root
	
	// Facebook settings
	, 'fb_app_id' => ''
	, 'fb_app_secret' => ''
	, 'fb_app_url' => 'https://apps.facebook.com/myapp-local'

)));

// Development
Environment::setConfig('development', array_merge( $globalConfig, array(
	// Pathing
	'base_url' => '//dev.host.com/' // relative reference uri for this site
	, '~' => '/project/folder' // url path, relative to the the web root
	
	// Config app for facebook use
	, 'fb_app_id' => ''
	, 'fb_app_secret' => ''
	, 'fb_app_url' => 'https://apps.facebook.com/myapp-dev'

)));

// UAT
Environment::setConfig('uat', array_merge( $globalConfig, array(
	// Pathing
	'base_url' => '//uat.host.com/' // relative reference uri for this site
	, '~' => '/' // url path, relative to the the web root
	
	// Config app for facebook use
	, 'fb_app_id' => ''
	, 'fb_app_secret' => ''
	, 'fb_app_url' => 'https://apps.facebook.com/myapp-uat'

)));

// PRODUCTION!
Environment::setConfig('production', array_merge( $globalConfig, array(
	// Pathing
	'base_url' => '//host.com/' // relative reference uri for this site
	, '~' => '/' // url path, relative to the the web root
	
	// Config app for facebook use
	, 'fb_app_id' => ''
	, 'fb_app_secret' => ''
	, 'fb_app_url' => 'https://apps.facebook.com/myapp'

)));


// Environment configuration to use when running tests
Environment::setConfig('testing', array_merge( $globalConfig, array(
	// Pathing
	'base_url' => '//localhost/' // relative reference uri for this site
	, '~' => '/project/folder/' // url path, relative to the the web root
	
	// Facebook settings
	, 'fb_app_id' => ''
	, 'fb_app_secret' => ''
	, 'fb_app_url' => 'https://apps.facebook.com/myapp-local'

)));

?>