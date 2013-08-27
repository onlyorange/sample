<?php
/**
 * @version   0.1.0
 * @author    http://devjuhong.com <zzeppon@hotmail.com>
 */

class store_shopSettings_Block_Adminhtml_Activate_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $isElementDisabled = false;
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('adminhtml')->__('Activate Parameters')));

        /**
         * Check is single store mode
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('store_id', 'multiselect', array(
                'name'      => 'stores[]',
                'label'     => Mage::helper('cms')->__('Store View'),
                'title'     => Mage::helper('cms')->__('Store View'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
                'disabled'  => $isElementDisabled
            ));
        }
        else {
            $fieldset->addField('store_id', 'hidden', array(
                'name'      => 'stores[]',
                'value'     => 0
            ));
        }

        $fieldset->addField('update_currency', 'checkbox', array(
            'label' => Mage::helper('shopsettings')->__('Update Currency'),
            'required' => false,
            'name' => 'update_currency',
            'value' => 1,
            'note' => Mage::helper('shopsettings')->__('Select if you wish to update allowed currencies to USD, EUR, POUND'),
        ))->setIsChecked(1);

        $fieldset->addField('setup_cms', 'checkbox', array(
            'label' => Mage::helper('shopsettings')->__('Create Cms Pages & Blocks'),
            'required' => false,
            'name' => 'setup_cms',
            'value' => 1,
        ))->setIsChecked(1);

        $form->setAction($this->getUrl('*/*/activate'));
        $form->setMethod('post');
        $form->setUseContainer(true);
        $form->setId('edit_form');

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
