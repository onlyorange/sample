<?php
/**
 * @version   0.1.0
 * @author    http://devjuhong.com <zzeppon@hotmail.com>
 */

$installer = $this;
$installer->startSetup();
$installer->getConnection()->addColumn($installer->getTable('shopcategories/scheme'), 'content_bg_img', 'varchar(255) NOT NULL after `content_bg`');
$installer->getConnection()->addColumn($installer->getTable('shopcategories/scheme'), 'content_bg_img_mode', 'varchar(8) NOT NULL after `content_bg_img`');
$installer->endSetup();