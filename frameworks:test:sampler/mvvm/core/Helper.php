<?php
namespace jlmvc\core;

use jlmvc\core\Config;

class sys
{
	#---------------------------
	#region Path Helpers
	#---------------------------
	
	/**
	 * Dynamically builds a path from all the differnet path args
	 */
	public static function makePath( )
	{
		return self::_makePath( func_get_args() ) . DIRECTORY_SEPARATOR;
	}
	
	/**
	 * Dynamically builds a file path from all the differnet path args
	 */
	public static function makeFilePath( )
	{
		return self::_makePath( func_get_args() );
	}	
	/**
	 * Helper for building various paths
	 */
	private static function _makePath( $args )
	{
		$count = count( $args );
		if( $count === 0 )
			return '';
		$path = $args[0];
		for( $i = 1; $i < $count; $i++) {
			$path .= DIRECTORY_SEPARATOR . $args[$i];
		}
		return $path;
		
	}

	#----------------------------
	#endregion
	#---------------------------


	#---------------------------
	#region Include Helpers
	#---------------------------
	
	/**
	 * Includes a library into the current stack
	 */
	public static function includeLibrary( $libraryName, $bootstrapFile = null )
	{
		if( is_null( $bootstrapFile ) )
			$bootstrapFile = $libraryName . Config::$phpExtension;
		
		include( self::makeFilePath( 
			BASE_PATH
			, Config::$libraryPath
			, $libraryName
			, $bootstrapFile
		));
	}

	/**
	 * Includes a test into the current stack
	 */
	public static function includeTest( $testName )
	{
		include( self::makeFilePath( BASE_PATH, 'tests', "$testName.php" ));
	}

	/**
	 * Includes a controller into the current stack
	 */
	public static function includeController( $testName )
	{
		include( self::makeFilePath( Config::$libraryPath, $libraryName ));
	}

	
	/**
	 * Gets the full path to a view file based on its controller & action 
	 */
	public static function getViewPath( $controllerName, $actionName )
	{
		return self::makeFilePath( 
			BASE_PATH
			, Config::$appPath
			, Config::$viewPath
			, $controllerName
			, $actionName . Config::$viewExtension
		);
	}

	/**
	 * Includes a view into the current stack
	 */
	public static function includeView( $controllerName, $actionName )
	{
		include( self::getViewPath( $controllerName, $actionName ) );
	}

	/**
	 * Includes a layout for rendering into the current stack
	 */
	public static function includeLayout( $layout = null )
	{
		if( is_null( $layout ) )
			$layout = Config::$defaultLayout;
		
		include( self::makeFilePath( 
			BASE_PATH
			, Config::$appPath
			, Config::$viewPath
			, Config::$layoutPath
			, $layout . Config::$viewExtension
		));
	}

}

?>