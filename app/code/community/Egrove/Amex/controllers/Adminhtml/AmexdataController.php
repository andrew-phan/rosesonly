<?php

class Egrove_Amex_Adminhtml_AmexdataController extends Mage_Adminhtml_Controller_action
{

        protected function _initAction() {
		$this->loadLayout()
		->_setActiveMenu('amex/amexdata')
		->_addBreadcrumb(Mage::helper('adminhtml')->__('Amex Response Log'), Mage::helper('adminhtml')->__('Amex Response Log'));
                return $this;
	}

	public function indexAction() {
		$this->_initAction()
		->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('amex/amexdata')->load($id);
	
		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}
	
			Mage::register('amexdata_data', $model);
	
			$this->loadLayout();
			$this->_setActiveMenu('amex/amexdata');
	
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
	
			$this->_addContent($this->getLayout()->createBlock('amex/adminhtml_amexdata_edit'))
			->_addLeft($this->getLayout()->createBlock('amex/adminhtml_amexdata_edit_tabs'));
	
			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('amex')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}

	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('amex/amexdata');

				$model->setId($this->getRequest()->getParam('id'))
				->delete();

				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Log was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

	public function massDeleteAction() {
		$amexdataIds = $this->getRequest()->getParam('amex');
		if(!is_array($amexdataIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
		} else {
			try {
				foreach ($amexdataIds as $amexdataId) {
					$deletedata = Mage::getModel('amex/amexdata')->load($amexdataId);
					$deletadata->delete();
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(
				Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($amexdataIds)
				)
				);
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}

}