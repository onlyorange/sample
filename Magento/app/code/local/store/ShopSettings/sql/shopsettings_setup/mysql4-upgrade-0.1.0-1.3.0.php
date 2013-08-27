<?php
/**
 * @version   0.1.0
 * @author    http://devjuhong.com <zzeppon@hotmail.com>
 */


try {
//create left col home page if not exist
    $is_page_exist = Mage::getModel('cms/page')->getCollection()
        ->addFieldToFilter('identifier', 'shop_home_2col_left')
        ->load();

    if ( !count($is_page_exist) ) {
        $cmsPage = array(
            'title' => 'shop Home page - left column',
            'identifier' => 'shop_home_2col_left',
            'content' => '<div class="home-left-col clearfix">
<div class="home-main">{{block type="shopsettings/product_list" category_id="12" num_products="6" template="catalog/product/featured_products.phtml"}}</div>
<div class="home-left">{{block type="cms/block" block_id="shop_banners_slideshow" }} {{block type="newsletter/subscribe" template="newsletter/subscribe_home.phtml" }} {{block type="shopsettings/bestsellers" template="store/bestsellers.phtml" }}</div>
</div></div>',
            'is_active' => 1,
            'sort_order' => 0,
            'stores' => array(0),
            'root_template' => 'one_column'
        );
        Mage::getModel('cms/page')->setData($cmsPage)->save();
    }

}
catch (Exception $e) {
    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('An error occurred while updating shop theme pages and cms blocks.'));
}