<?php
/**
 * @version   0.1.0
 * @author    http://devjuhong.com <zzeppon@hotmail.com>
 */

class store_shopcategories_Block_Adminhtml_shopcategories_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'shopcategories';
        $this->_controller = 'adminhtml_shopcategories';
        
        $this->_updateButton('save', 'label', Mage::helper('shopcategories')->__('Save Scheme'));
        $this->_updateButton('delete', 'label', Mage::helper('shopcategories')->__('Delete Scheme'));
		
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
        if( Mage::registry('shopcategories_data') && Mage::registry('shopcategories_data')->getId() ) {
            return Mage::helper('shopcategories')->__("Edit Scheme '%s'", $this->escapeHtml(Mage::registry('shopcategories_data')->getCategoryId()));
        } else {
            return Mage::helper('shopcategories')->__('Add Scheme');
        }
    }
}