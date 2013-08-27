<?php
/**
 * @version   0.1.0
 * @author    http://devjuhong.com <zzeppon@hotmail.com>
 */

class store_shopslideshow_Model_Config_Yesno
{
    public function toOptionArray()
    {
	    $options = array();
	    $options[] = array(
            'value' => 'true',
            'label' => 'Yes',
        );
        $options[] = array(
            'value' => 'false',
            'label' => 'No',
        );

        return $options;
    }

}
