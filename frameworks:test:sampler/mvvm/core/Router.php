<?php
namespace jlmvc\core;

use jlmvc\core\Request;
use jlmvc\core\Response;
use jlmvc\core\Config;
use jlmvc\core\Helper;
use jlmvc\core\View;
use jlmvc\core\Environment;
use jlmvc\template\view\IRenderer;
use jlmvc\template\view\TwigRenderer;

class Router 
{
	public static function route(
		Request $request
		,IRenderer $renderer = null // TODO: IoC it
		)
	{
		// Bootstrap the environment here?
		// TODO
		Environment::setByRequest( $request );

		// intercept any testing requests
		if( $request->controllerName == Config::$testControllerName ) 
		{
			Environment::setEnv( Config::$testingEnv ); // default to this env
			sys::includeLibrary( Config::$unitTestInclude );
			
			if( $request->actionName === '' )
				sys::includeTest( 'runall' );
			else
				sys::includeTest( $request->actionName );
			die();
		}

		// ensure a default renderer
		if( is_null( $renderer ) )
			$renderer = new TwigRenderer();

		// TODO:
		// handle any filters that need to get run before the action gets executed

			
		// TODO: routing logic
		
		// if there's no url params, route to the default action
		
		// if there's one url param, route to the default action on that controller
		
		// if there's two url params, route to that action on the controller
		
		// if more then two, route to the action on the controller, and map the rest to the model for that controller
		
		if( isset($request->controllerName) && $request->controllerName !== '' )
			$controller = Router::getController( $request->controllerName );
		else
			$controller = Router::getController( 'home' ); // TODO: use a config value for this
		
		//
		// TODO: figure out a better spot for this 404 logic
		// technically this should be the job of the render?
		// or maybe we return the reposnse here, and leave it up to caller of this to handle
		//
		if( is_null($controller) ) // TODO: 404 handlers
			return new Response( array( 'code' => 404, 'message' => 'Controller not found' ) );
		
		$action = $request->actionName;
		if( $action === '' || is_null( $action ) )
			$action = Config::$defaultControllerAction;
		
		if( !method_exists( $controller, $action ) )
			return new Response( array( 'code' => 404, 'message' => 'Controller action not a function' ) );

		$controller->setAction( $action ); // Store this to the meta so we could look up later
		$result = $controller->$action();

		// TODO: handle redirects, etc first
		// Get the response from the renderer
		$response = $renderer->render( $controller, array(
			'title' => 'my title'
			, 'assets' => '/app/webroot/'
			, 'root' => '/'
			, 'message' => $result
		));

		// TODO:
		// handle any filters that need to get run AFTER the action gets executed

		return $response;
	}
	
	
	/**
	 * Determines some meta information about a controller based on its name
	 * 
	 * @var string
	 * @return array
	 */
	public static function getControllerInfo( $controllerName )
	{
		$className = $controllerName . Config::$controllerPostfix;
			
		return array(
			'name' => $controllerName
			, 'className' => $className
			, 'namespacedName' => 'app\\controllers\\' . $className
			, 'path' => Config::getControllerPath() . $className . Config::$phpExtension
			
		);
	}
	
	/**
	 * Gets a new instance of the controller by its conical name
	 * 
	 * @var string
	 * @var bool -- pass in as true if there's a chance the controller file will get included more than once
	 *				(this was added for testing purposes)
	 * @return controller
	 */
	public static function getController($controllerName, $include_once = false)
	{
		$meta = self::getControllerInfo($controllerName);
		
		if (file_exists($meta['path'])) {
			if( $include_once )
				include_once($meta['path']);
			else
				include($meta['path']);
			
			$className = $meta['namespacedName'];

			$controller = new $className();
			$controller->setMeta( $meta );

			//$controller->setControllerName($controllerName);
			//$controller->setControllerSubPath($controllerSubPath);
			return $controller;
		}
		error_log("$controllerName not found at path: '" . $meta['path'] . "'");
		return null;
		//throw new \Exception("$controllerName not found at path: '" . $meta['path'] . "'");
	}
}
?>