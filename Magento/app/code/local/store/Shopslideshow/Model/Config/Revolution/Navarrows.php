<?php
/**
 * @version   0.1.0
 * @author    http://devjuhong.com <zzeppon@hotmail.com>
 */

class store_shopslideshow_Model_Config_Revolution_Navarrows
{
    public function toOptionArray()
    {
	    $options = array();
        $options[] = array(
            'value' => 'none',
            'label' => 'none',
        );
        $options[] = array(
            'value' => 'verticalcentered',
            'label' => 'verticalcentered',
        );

        return $options;
    }

}
