<?php
/**
 * @version   0.1.0
 * @author    http://devjuhong.com <zzeppon@hotmail.com>
 */

class store_shopslideshow_Block_Slideshow extends Mage_Core_Block_Template
{
	protected function _beforeToHtml()
	{
		$config = Mage::getStoreConfig('shopslideshow', Mage::app()->getStore()->getId());
		if (Mage::helper('shopslideshow/data')->isSlideshowEnabled()) {
			$this->setTemplate('store/' . $config['config']['slider'] . '.phtml');
		}

		return $this;
	}

	public function _prepareLayout()
	{
		return parent::_prepareLayout();
	}

	public function getSlideshow()
	{
		if (!$this->hasData('shopslideshow')) {
			$this->setData('shopslideshow', Mage::registry('shopslideshow'));
		}
		return $this->getData('shopslideshow');

	}

	public function getSlides()
	{
		$config = Mage::getStoreConfig('shopslideshow', Mage::app()->getStore()->getId());
		if ( $config['config']['slider'] == 'flexslider' ) {
			$model = Mage::getModel('shopslideshow/shopslideshow');
		} else {
			$model = Mage::getModel('shopslideshow/shoprevolution');
		}
		$slides = $model->getCollection()
			->addStoreFilter(Mage::app()->getStore())
			->addFieldToSelect('*')
			->addFieldToFilter('status', 1)
			->setOrder('sort_order', 'asc');
		return $slides;
	}

}