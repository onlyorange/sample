<?php
/**
 * @version   0.1.0
 * @author    http://devjuhong.com <zzeppon@hotmail.com>
 */

class store_shopSettings_Block_Media extends Mage_Catalog_Block_Product_View_Media
{

    protected function _beforeToHtml()
    {
        if (Mage::getStoreConfig('shopsettings/images/zoom', Mage::app()->getStore()->getId()) == 'default') {
            return;
        }
        if (Mage::getStoreConfig('shopsettings/images/zoom', Mage::app()->getStore()->getId()) == 'lightbox') {
            $this->setTemplate('store/lightbox/media.phtml');
        }
        if (Mage::getStoreConfig('shopsettings/images/zoom', Mage::app()->getStore()->getId()) == 'cloud_zoom'
            && Mage::getStoreConfig('shopsettings/cloudzoom/enabled', Mage::app()->getStore()->getId())) {
            $this->setTemplate('store/cloudzoom/media.phtml');
        }
        return $this;
    }
}