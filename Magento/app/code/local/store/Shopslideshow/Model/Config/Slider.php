<?php
/**
 * @version   0.1.0
 * @author    http://devjuhong.com <zzeppon@hotmail.com>
 */

class store_shopslideshow_Model_Config_Slider
{
    public function toOptionArray()
    {
	    $options = array();
	    $options[] = array(
            'value' => 'flexslider',
            'label' => 'Flexslider',
        );
        $options[] = array(
            'value' => 'revolution',
            'label' => 'Revolution slider',
        );

        return $options;
    }

}
