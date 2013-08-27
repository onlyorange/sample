<?php
/**
 * @version   0.1.0
 * @author    http://devjuhong.com <zzeppon@hotmail.com>
 */

class store_shopslideshow_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function isSlideshowEnabled()
	{
		$config = Mage::getStoreConfig('shopslideshow', Mage::app()->getStore()->getId());
		$request = Mage::app()->getFrontController()->getRequest();
		$route = Mage::app()->getFrontController()->getRequest()->getRouteName();
		$action = Mage::app()->getFrontController()->getRequest()->getActionName();
		$show = false;
		if ($config['config']['enabled']) {
			$show = true;
			if ($config['config']['show'] == 'home') {
				$show = false;
				if ($request->getModuleName() == 'cms' && $request->getControllerName() == 'index' && $request->getActionName() == 'index') {
					$show = true;
				}
			}
			if ($show && ($route == 'customer' && ($action == 'login' || $action == 'forgotpassword' || $action == 'create'))) {
				$show = false;
			}
		}
		return $show;
	}
}