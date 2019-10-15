<?php

class Ant_Deliverymanagement_Adminhtml_DeliverymanagementController extends Mage_Adminhtml_Controller_Action {

    protected function _initAction() {
        $this->loadLayout()->_setActiveMenu("deliverymanagement/deliverymanagement")->_addBreadcrumb(Mage::helper("adminhtml")->__("Deliverymanagement  Manager"), Mage::helper("adminhtml")->__("Deliverymanagement Manager"));
        return $this;
    }

    public function indexAction() {
        $this->_initAction();
        $this->renderLayout();
    }

    public function disableAction() {
        $shipmentIds = $this->getRequest()->getParam("assign_id");

        if (!is_array($shipmentIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('tax')->__('Please select shipment.'));
        } else {
            try {
                foreach ($shipmentIds as $shipmentId) {
                    $shipment = Mage::getModel('deliverymanagement/deliverymanagement')->load($shipmentId);
                    $shipment->setUpdatestatus(0);
                    $shipment->save();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess('Total of '.count($shipmentIds).' shipment(s) were updated.');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

}
