<?php

class store_shopSettings_Model_Config_Upsell
{
    public function toOptionArray()
    {
        return array(
            array(
                'value'=>'never',
                'label' => Mage::helper('shopsettings')->__('Never Replace Upsell Products')),
            array(
                'value'=>'always',
                'label' => Mage::helper('shopsettings')->__('Always Replace Upsell Products')),
            array(
                'value'=>'only',
                'label' => Mage::helper('shopsettings')->__('Replace Only if No Upsell Products')),
        );
    }
}