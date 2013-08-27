<?php
/**
 * @version   0.1.0
 * @author    http://devjuhong.com <zzeppon@hotmail.com>
 */

class store_shopSettings_Block_Bestsellers extends Mage_Catalog_Block_Product_Abstract
{
    public function __construct(){
        parent::_construct();
        $this->setData('bestsellers', Mage::getStoreConfig('shopsettings/catalog/bestsellers'));
    }

    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    public function getBestsellers()
    {
        $id = $this->getData('bestsellers');
        if (  empty($id) ) return null;

	    $productIds = explode(',',$this->getData('bestsellers'));
        $products = Mage::getModel("catalog/product")
		    ->getCollection()
	        ->addStoreFilter()
		    ->addAttributeToSelect("*")
		    ->addAttributeToFilter('entity_id', array('in' => $productIds));

        return $products;
    }
}