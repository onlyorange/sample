<?php
/**
 * @version   0.1.0
 * @author    http://devjuhong.com <zzeppon@hotmail.com>
 */

class store_shopslideshow_Block_Adminhtml_shoprevolution extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'adminhtml_shoprevolution';
		$this->_blockGroup = 'shopslideshow';
		$this->_headerText = Mage::helper('shopslideshow')->__('Revolution Slides Manager');
		$this->_addButtonLabel = Mage::helper('shopslideshow')->__('Add Slide');
		parent::__construct();
	}
}