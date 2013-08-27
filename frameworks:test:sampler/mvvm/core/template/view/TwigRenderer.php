<?php
namespace jlmvc\template\view;

use jlmvc\core\Config;
use jlmvc\core\Controller;
use jlmvc\core\sys;
use jlmvc\core\Response;

class TwigRenderer implements IRenderer
{
	protected $view = '';

	public function __construct( )
	{
		$this->renderer = new \Twig_Environment(
			new \Twig_Loader_Filesystem(
				sys::makePath( BASE_PATH, Config::$appPath , Config::$viewPath )
			)
		);
	}

	/**
	 *	Renders the current controller context to a template
	 *
	 */
	public function render( 
			$controller
			, array $params = null
		)
	{
		try
		{
			$this->view = $this->renderer->loadTemplate(
				sys::makeFilePath(
					$controller->getName()
					, $controller->getAction() . Config::$viewExtension
				)
			);
		}
		catch( \Exception $e )
		{
			return new Response( array(
				'code' => 404
				, 'message' => 'View file not found'
			));
		}

		$this->view->display($params);
		return new Response();
	}
}

?>