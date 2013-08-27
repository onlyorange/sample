<?php
/**
 * @version   0.1.0
 * @author    http://devjuhong.com <zzeppon@hotmail.com>
 */

class store_shopslideshow_Block_Adminhtml_shopslideshow_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'shopslideshow';
        $this->_controller = 'adminhtml_shopslideshow';
        
        $this->_updateButton('save', 'label', Mage::helper('shopslideshow')->__('Save Slide'));
        $this->_updateButton('delete', 'label', Mage::helper('shopslideshow')->__('Delete Slide'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('shopslideshow_data') && Mage::registry('shopslideshow_data')->getId() ) {
            return Mage::helper('shopslideshow')->__("Edit Slide");
        } else {
            return Mage::helper('shopslideshow')->__('Add Slide');
        }
    }
}