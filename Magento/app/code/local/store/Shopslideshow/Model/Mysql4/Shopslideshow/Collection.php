<?php
/**
 * @version   0.1.0
 * @author    http://devjuhong.com <zzeppon@hotmail.com>
 */

class store_shopslideshow_Model_Mysql4_shopslideshow_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('shopslideshow/shopslideshow');
    }

	/**
     * Add Filter by store
     *
     * @param Mage_Core_Model_Store $store
	 * @param bool $withAdmin
	 * @return store_shopslideshow_Model_Mysql4_shopslideshow_Collection
	 */
	public function addStoreFilter($store, $withAdmin = true)
	{
		if ($store instanceof Mage_Core_Model_Store) {
			$store = array($store->getId());
		}

		$this->getSelect()->join(
			array('store_table' => $this->getTable('shopslideshow/slides_store')),
			'main_table.slide_id = store_table.slide_id',
			array()
		)
		->where('store_table.store_id in (?)', ($withAdmin ? array(0, $store) : $store));

		return $this;
	}
}