<?php
/**
 * @version   0.1.0
 * @author    http://devjuhong.com <zzeppon@hotmail.com>
 */

class store_shopslideshow_Model_Config_Revolution_Onoff
{
    public function toOptionArray()
    {
	    $options = array();
	    $options[] = array(
            'value' => 'on',
            'label' => 'On',
        );
        $options[] = array(
            'value' => 'off',
            'label' => 'Off',
        );

        return $options;
    }

}
