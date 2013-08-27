<?php
/**
 * @version   0.1.0
 * @author    http://devjuhong.com <zzeppon@hotmail.com>
 */

class store_shopslideshow_Model_Config_Revolution_Timeline
{
    public function toOptionArray()
    {
	    $options = array();
	    $options[] = array(
            'value' => 'top',
            'label' => 'top',
        );
        $options[] = array(
            'value' => 'bottom',
            'label' => 'bottom',
        );

        return $options;
    }

}
