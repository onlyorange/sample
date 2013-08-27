<?php
/**
 * @version   0.1.0
 * @author    http://devjuhong.com <zzeppon@hotmail.com>
 */

class store_shopcategories_Block_Adminhtml_shopcategories_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('shopcategories_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('shopcategories')->__('Scheme Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('shopcategories')->__('Scheme Information'),
          'title'     => Mage::helper('shopcategories')->__('Scheme Information'),
          'content'   => $this->getLayout()->createBlock('shopcategories/adminhtml_shopcategories_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}