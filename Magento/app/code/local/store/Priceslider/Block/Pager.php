<?php

class store_Priceslider_Block_Pager extends Mage_Page_Block_Html_Pager
{

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('store/priceslider/slider_pager.phtml');
    }

}