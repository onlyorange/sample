<?php
namespace jlmvc\template\view;

use jlmvc\core\Controller;

interface IRenderer
{
	public function __construct();

	public function render( $controller, array $params );
}

?>