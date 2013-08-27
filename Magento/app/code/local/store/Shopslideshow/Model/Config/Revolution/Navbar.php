<?php
/**
 * @version   0.1.0
 * @author    http://devjuhong.com <zzeppon@hotmail.com>
 */

class store_shopslideshow_Model_Config_Revolution_Navbar
{
    public function toOptionArray()
    {
	    $options = array();
        $options[] = array(
            'value' => 'none',
            'label' => 'none',
        );
	    $options[] = array(
            'value' => 'bullet',
            'label' => 'bullet',
        );
        $options[] = array(
            'value' => 'thumb',
            'label' => 'thumb',
        );
        $options[] = array(
            'value' => 'both',
            'label' => 'both',
        );

        return $options;
    }

}
