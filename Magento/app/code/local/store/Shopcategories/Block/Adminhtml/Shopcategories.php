<?php
/**
 * @version   0.1.0
 * @author    http://devjuhong.com <zzeppon@hotmail.com>
 */

class store_shopcategories_Block_Adminhtml_shopcategories extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'adminhtml_shopcategories';
		$this->_blockGroup = 'shopcategories';
		$this->_headerText = Mage::helper('shopcategories')->__('Color Scheme Manager');
		$this->_addButtonLabel = Mage::helper('shopcategories')->__('Add Scheme');
		parent::__construct();
	}
}