<?php
/**
 * @version   0.1.0
 * @author    http://devjuhong.com <zzeppon@hotmail.com>
 */

class store_shopSettings_Model_Config_Position
{

    public function toOptionArray()
    {
        return array(
            array(
	            'value'=>'top-left',
	            'label' => Mage::helper('shopsettings')->__('Top Left')),
            array(
	            'value'=>'top-right',
	            'label' => Mage::helper('shopsettings')->__('Top Right')),
        );
    }

}
