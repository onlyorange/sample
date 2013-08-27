<?php
/**
 * @version   0.1.0
 * @author    http://devjuhong.com <zzeppon@hotmail.com>
 */

class store_shopslideshow_Block_Adminhtml_shopslideshow_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('shopslideshow_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('shopslideshow')->__('Flexslider Slide Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('shopslideshow')->__('Flexslider Slide Information'),
          'title'     => Mage::helper('shopslideshow')->__('Flexslider Slide Information'),
          'content'   => $this->getLayout()->createBlock('shopslideshow/adminhtml_shopslideshow_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}