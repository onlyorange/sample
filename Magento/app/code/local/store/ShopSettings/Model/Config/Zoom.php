<?php
/**
 * @version   0.1.0
 * @author    http://devjuhong.com <zzeppon@hotmail.com>
 */

class store_shopSettings_Model_Config_Zoom
{

    public function toOptionArray()
    {
        return array(
            array(
	            'value'=>'default',
	            'label' => Mage::helper('shopsettings')->__('Magento Default')),
            array(
	            'value'=>'cloud_zoom',
	            'label' => Mage::helper('shopsettings')->__('CloudZoom')),
            array(
	            'value'=>'lightbox',
	            'label' => Mage::helper('shopsettings')->__('Lightbox')),
        );
    }

}
