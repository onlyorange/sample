<?php
namespace jlmvc\core;

use jlmvc\core\sys;

class Config
{
	#---------------------------
	#region Core Attributes
	#---------------------------
	
	public static $phpExtension = '.php';
	public static $controllerPostfix = 'Controller';
	public static $defaultControllerAction = 'index';

	#endregion
	
	
	#---------------------------
	#region Path Variables
	#---------------------------
	
	/**
	 * The folder where 3rd party libraries are included
	 */
	public static $libraryPath = 'vendor';
	
	/**
	 * The folder where all your application resides
	 */
	public static $appPath = 'app';
	
	/**
	 * The folder where all the controllers are located, relative to the $appPath
	 */
	public static $controllerPath = 'controllers';
	
	/**
	 * The folder where all the views are located, relative to the $appPath
	 */
	public static $viewPath = 'views';
	
	/**
	 * The folder where all the models are located, relative to the $appPath
	 */
	public static $modelPath = 'models';

	public static function getControllerPath()
	{
		return sys::makePath(BASE_PATH, self::$appPath, self::$controllerPath);
	}
	public static function getViewPath()
	{
		return sys::makePath(BASE_PATH, self::$appPath, self::$viewPath);
	}
	public static function getModelPath()
	{
		return sys::makePath(BASE_PATH, self::$appPath, self::$modelPath);
	}

	#endregion

	#---------------------------
	#region Unit Test Helpers
	#---------------------------
	
	/**
	 * This the file that all unit tests should inherit from. 
	 * File name should be relative to the libraries directory
	 */
	public static $unitTestInclude = 'vUnitPhp';
	public static $testPostfix = 'Test';
	public static $testControllerName = 'tests';

	#endregion

	#---------------------------
	#region Layout and View Settings
	#---------------------------

	/**
	 * The folder where all layout files are stored, relative to the views folder
	 */
	public static $layoutPath = 'shared';
	
	/**
	 * The extension to use for all view files
	 */
	public static $viewExtension = '.html.php';
	
	/**
	 * The name of the default layout file to use, if not otherwise specified
	 */
	public static $defaultLayout = 'layout';

	#endregion


	#---------------------------
	#region Some env helpers
	#---------------------------

	public static $localEnv = 'local';
	public static $developmentEnv = 'development';
	public static $uatEnv = 'uat';
	public static $productionEnv = 'production';
	public static $testingEnv = 'testing';

	#---------------------------
	#endregion
	#---------------------------

}

?>