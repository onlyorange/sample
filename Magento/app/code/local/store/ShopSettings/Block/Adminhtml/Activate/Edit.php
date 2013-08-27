<?php
/**
 * @version   0.1.0
 * @author    http://devjuhong.com <zzeppon@hotmail.com>
 */

class store_shopSettings_Block_Adminhtml_Activate_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_blockGroup = 'shopsettings';
        $this->_controller = 'adminhtml_activate';
        $this->_updateButton('save', 'label', Mage::helper('shopsettings')->__('Activate shop Theme'));
        $this->_removeButton('delete');
        $this->_removeButton('back');
    }

    public function getHeaderText()
    {
        return Mage::helper('shopsettings')->__('Activate shop Theme');
    }
}
