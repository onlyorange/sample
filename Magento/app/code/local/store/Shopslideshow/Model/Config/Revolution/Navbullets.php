<?php
/**
 * @version   0.1.0
 * @author    http://devjuhong.com <zzeppon@hotmail.com>
 */

class store_shopslideshow_Model_Config_Revolution_Navbullets
{
    public function toOptionArray()
    {
	    $options = array();
        $options[] = array(
            'value' => 'round',
            'label' => 'round',
        );
	    $options[] = array(
            'value' => 'navbar',
            'label' => 'navbar',
        );
        $options[] = array(
            'value' => 'round-old',
            'label' => 'round-old',
        );
        $options[] = array(
            'value' => 'square-old',
            'label' => 'square-old',
        );
        $options[] = array(
            'value' => 'navbar-old',
            'label' => 'navbar-old',
        );

        return $options;
    }

}