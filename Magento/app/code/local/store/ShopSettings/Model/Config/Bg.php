<?php
/**
 * @version   0.1.0
 * @author    http://devjuhong.com <zzeppon@hotmail.com>
 */

class store_shopSettings_Model_Config_Bg
{

    public function toOptionArray()
    {
        return array(
            array(
	            'value'=>'stretch',
	            'label' => Mage::helper('shopsettings')->__('stretch')),
            array(
	            'value'=>'tile',
	            'label' => Mage::helper('shopsettings')->__('tile')),
        );
    }

}
