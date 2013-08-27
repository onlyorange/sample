<?php
/**
 * @version   0.1.0
 * @author    http://devjuhong.com <zzeppon@hotmail.com>
 */

class store_shopSettings_Model_Config_Zoom_Position
{

    public function toOptionArray()
    {
        return array(
            array(
	            'value'=>'right',
	            'label' => Mage::helper('shopsettings')->__('Right')),
            array(
	            'value'=>'inside',
	            'label' => Mage::helper('shopsettings')->__('Inside')),
        );
    }

}
