<?php
namespace jlmvc\core;

/**
 * Base class to use when instaniating an instance of a regular mvc controller
 * 
 */
 
 
class BaseController
{
	/**
	 * Arrays are fast in php?
	 */
	protected $_meta = array();
	
	public function setMeta( array $meta ) {
		$this->_meta = $meta;
	}
	
	public function getName( ) {
		return $this->_meta['name'];
	}
	
	public function getClassName() {
		return $this->_meta['className'];
	}
	
	public function getPath( ) {
		return $this->_meta['path'];
	}

	/**
	 * Currently requested action, if any
	 */
	public function getAction( ) {
		return $this->_meta['action'];
	}
	public function setAction( $actionName ) {
		$this->_meta['action'] = $actionName;
	}
}

/**
 * Base class to use when you're making an REST style API controller 
 */
 
class WebController extends BaseController
{
	
}
 
class ApiController extends BaseController
{
	
}




?>