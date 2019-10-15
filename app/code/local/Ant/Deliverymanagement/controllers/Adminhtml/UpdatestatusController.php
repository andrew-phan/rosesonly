<?php

class Ant_Deliverymanagement_Adminhtml_UpdatestatusController extends Mage_Adminhtml_Controller_Action {

    protected function _initAction() {
        $this->loadLayout()->_setActiveMenu("deliverymanagement/updatestatus")->_addBreadcrumb(Mage::helper("adminhtml")->__("Updatestatus  Manager"), Mage::helper("adminhtml")->__("Updatestatus Manager"));
        return $this;
    }

    public function indexAction() {
        $this->_initAction();
        $this->renderLayout();
    }
    
    public function updateAction() {
        $status = $this->getRequest()->getParam("status");
        $shipmentIds = $this->getRequest()->getParam("assign_id");

        if (!is_array($shipmentIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('tax')->__('Please select shipment.'));
        } else {
            try {
                $count = 0;
                foreach ($shipmentIds as $shipmentId) {
                    $shipment = Mage::getModel('deliverymanagement/deliverymanagement')->load($shipmentId);
                    if($shipment->getStatus() != 'completed') {
                        $shipment->setStatus($status);
                        $shipment->save();
                        $count++;
                    }
                }
                Mage::getSingleton('adminhtml/session')->addSuccess('Total of '.$count.' shipment(s) were updated.');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

}
