<?php
/**
 * @version   0.1.0
 * @author    http://devjuhong.com <zzeppon@hotmail.com>
 */

class store_shopslideshow_Block_Adminhtml_shoprevolution_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('shoprevolution_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('shopslideshow')->__('Revolution Slide Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('shopslideshow')->__('Revolution Slide Information'),
          'title'     => Mage::helper('shopslideshow')->__('Revolution Slide Information'),
          'content'   => $this->getLayout()->createBlock('shopslideshow/adminhtml_shoprevolution_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}