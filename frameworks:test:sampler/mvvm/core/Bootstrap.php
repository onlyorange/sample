<?php
error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);

#---------------------------
#region Bootstrap in all the core files
#---------------------------

require __DIR__ . '/Helper.php';
require __DIR__ . '/Config.php';
require __DIR__ . '/Request.php';
require __DIR__ . '/Controller.php';
require __DIR__ . '/Router.php';
require __DIR__ . '/Response.php';
require __DIR__ . '/Environment.php';

#endregion


#------------------------------------------------------
#region Autoload any vendor libraries (via composer)
#------------------------------------------------------

require BASE_PATH . '/vendor/autoload.php';

#endregion


#---------------------------
#region Templates & Views
#---------------------------

/**
 *	These are used to render and display views throughout the application
 *	By default, we use Twig for rendering, but an interface is provided such
 *	that any templating engine can be used
 */

//require __DIR__ . '/View.php';
require __DIR__ . '/template/view/IRenderer.php';
require __DIR__ . '/template/view/TwigRenderer.php';

/**
 *	Twig specific bootstrap -- do this here so we don't accidently
 * 	include this file and register it more than once
 */
Twig_Autoloader::register();

#endregion

#
#region App specific configuration
#

use jlmvc\core\Config;

require BASE_PATH . '/' . Config::$appPath . '/config/environment.php';


#
#endregion
#


?>