<?php
/**
 * @version   0.1.0
 * @author    http://devjuhong.com <zzeppon@hotmail.com>
 */

$installer = $this;
$installer->startSetup();
$installer->getConnection()->addColumn($installer->getTable('shopcategories/scheme'), 'menu_text_color', 'char(7) NOT NULL after `header_bg`');
$installer->endSetup();