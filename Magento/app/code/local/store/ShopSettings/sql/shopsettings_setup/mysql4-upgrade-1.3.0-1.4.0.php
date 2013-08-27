<?php
$installer = $this;
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer->startSetup();
$installer->setConfigData('shopsettings/appearance/timeline', '#322c29');
$installer->setConfigData('shopsettings/design/search_field', '0');
$installer->setConfigData('shopsettings/design/below_logo', '0');
$installer->setConfigData('shopsettings/navigation/use_wide_navigation', '0');
$installer->endSetup();