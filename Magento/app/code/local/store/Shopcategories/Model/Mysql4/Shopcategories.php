<?php
/**
 * @version   0.1.0
 * @author    http://devjuhong.com <zzeppon@hotmail.com>
 */

class store_shopcategories_Model_Mysql4_shopcategories extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the scheme_id refers to the key field in your database table.
        $this->_init('shopcategories/scheme', 'scheme_id');
    }

	/**
	 * add scheme to store
     *
     * @param Mage_Core_Model_Abstract $object
	 * @return Mage_Core_Model_Abstract $object
     */
	protected function _afterSave(Mage_Core_Model_Abstract $object)
	{
		$condition = $this->_getWriteAdapter()->quoteInto('scheme_id = ?', $object->getId());
		$this->_getWriteAdapter()->delete($this->getTable('shopcategories/scheme_store'), $condition);

		if(!$object->getData('stores')){
			$object->setData('stores',$object->getData('store_id'));
		}

		if(in_array(0,$object->getData('stores'))){
			$object->setData('stores',array(0));
		}

		foreach ((array)$object->getData('stores') as $store) {
			$storeArray = array();
			$storeArray['scheme_id'] = $object->getId();
			$storeArray['store_id'] = $store;
			$this->_getWriteAdapter()->insert($this->getTable('shopcategories/scheme_store'), $storeArray);
		}

		return parent::_afterSave($object);
	}

	/**
     * add store data to scheme
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Core_Model_Abstract $object
     */
	protected function _afterLoad(Mage_Core_Model_Abstract $object)
	{
		// Liste des stores
		$select = $this->_getReadAdapter()->select()
			->from($this->getTable('shopcategories/scheme_store'))
			->where('scheme_id = ?', $object->getId());

		if ($data = $this->_getReadAdapter()->fetchAll($select)) {
			$storesArray = array();
			foreach ($data as $row) {
				$storesArray[] = $row['store_id'];
			}
			$object->setData('store_id', $storesArray);
		}

		return parent::_afterLoad($object);
	}

}