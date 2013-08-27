<?php
/**
 * @version   0.1.0
 * @author    http://devjuhong.com <zzeppon@hotmail.com>
 */

class store_shopslideshow_Model_Config_Show
{
    public function toOptionArray()
    {
	    $options = array();
	    $options[] = array(
            'value' => 'home',
            'label' => 'HomePage Only',
        );
        $options[] = array(
            'value' => 'all',
            'label' => 'All Pages',
        );

        return $options;
    }

}
