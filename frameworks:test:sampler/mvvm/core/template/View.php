<?php
namespace jlmvc\core;

class View
{
	protected $vars = array();
	public $viewModel = array();

	public function __construct() {
    
    }

	public function render( $viewPath )
  	{
  		if( file_exists( $viewPath ) )
  			include( $viewPath );
  		else
  			throw new \Exception("'$viewPath' not found!");
	}

	public function __set($name, $value) {
    	$this->vars[$name] = $value;
	}
	
	public function __get($name) {
    	return $this->vars[$name];
	}
}

?>