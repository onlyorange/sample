<?php
/**
 * @version   0.1.0
 * @author    http://devjuhong.com <zzeppon@hotmail.com>
 */

class store_shopcategories_Adminhtml_shopcategoriesController extends Mage_Adminhtml_Controller_Action
{

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')
            ->isAllowed('store/shop/shopcategories');
    }

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('store/shop/shopcategories')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Color Scheme Manager'), Mage::helper('adminhtml')->__('Color Scheme Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->_addContent($this->getLayout()->createBlock('shopcategories/adminhtml_shopcategories'))
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('shopcategories/shopcategories')->load($id);

		if ($model->getId() || $id == 0) {

			$this->_initAction();

			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('shopcategories_data', $model);

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('shopcategories/adminhtml_shopcategories_edit'))
				->_addLeft($this->getLayout()->createBlock('shopcategories/adminhtml_shopcategories_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('shopcategories')->__('Color Scheme does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {

            if(isset($_FILES['content_bg_img']['name']) && $_FILES['content_bg_img']['name'] != null) {
                $result['file'] = '';
                try {
                    /* Starting upload */
                    $uploader = new Varien_File_Uploader('content_bg_img');

                    // Any extention would work
                    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                    $uploader->setAllowRenameFiles(true);

                    // Set the file upload mode
                    // false -> get the file directly in the specified folder
                    // true -> get the file in the product like folders
                    //	(file.jpg will go in something like /media/f/i/file.jpg)
                    $uploader->setFilesDispersion(false);

                    // We set media as the upload dir
                    $path = Mage::getBaseDir('media') . DS.'store/shop'.DS ;
                    $result = $uploader->save($path, $_FILES['content_bg_img']['name'] );

                } catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage() . '  '. $path);
                    Mage::getSingleton('adminhtml/session')->setFormData($data);
                    $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                    return;
                }
                $data['content_bg_img'] = 'store/shop/'.$result['file'];
            }else {

                if(isset($data['content_bg_img']['delete']) && $data['content_bg_img']['delete'] == 1)
                    $data['content_bg_img'] = '';
                else
                    unset($data['content_bg_img']);
            }

			$model = Mage::getModel('shopcategories/shopcategories');
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));

			try {
				if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				}	
				
				$model->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('shopcategories')->__('Color Scheme was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('shopcategories')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('shopcategories/shopcategories');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Color Scheme was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $shopcategoriesIds = $this->getRequest()->getParam('shopcategories');
        if(!is_array($shopcategoriesIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select Color Scheme(s)'));
        } else {
            try {
                foreach ($shopcategoriesIds as $shopcategoriesId) {
                    $shopcategories = Mage::getModel('shopcategories/shopcategories')->load($shopcategoriesId);
                    $shopcategories->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($shopcategoriesIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    public function massStatusAction()
    {
        $shopcategoriesIds = $this->getRequest()->getParam('shopcategories');
        if(!is_array($shopcategoriesIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select Color Scheme(s)'));
        } else {
            try {
                foreach ($shopcategoriesIds as $shopcategoriesId) {
                    $shopcategories = Mage::getSingleton('shopcategories/shopcategories')
                        ->load($shopcategoriesId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($shopcategoriesIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

}