<?php

class Ant_Deliverymanagement_Adminhtml_ArrangedriverController extends Mage_Adminhtml_Controller_Action {

    protected function _initAction() {
        $this->loadLayout()->_setActiveMenu("deliverymanagement/arrangedriver")->_addBreadcrumb(Mage::helper("adminhtml")->__("Arrangedriver  Manager"), Mage::helper("adminhtml")->__("Arrangedriver Manager"));
        return $this;
    }

    public function indexAction() {
        $this->_initAction();
        $this->renderLayout();
    }

    public function assignAction() {
        $userId = $this->getRequest()->getParam("driver");
        $shipmentIds = $this->getRequest()->getParam("entity_id");

        if (!is_array($shipmentIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('tax')->__('Please select shipment.'));
        } else {
            try {
                $count = 0;
                foreach ($shipmentIds as $shipmentId) {
//                    $shipments = Mage::getResourceModel('sales/order_shipment_collection')
//                                    ->addAttributeToFilter('entity_id', $shipmentId)
//                                    ->getSelect()->joinleft(array('one_step' => 'mw_onestepcheckout'), 'one_step.sales_order_id = main_table.order_id', array('mw_deliverydate_date', 'mw_customercomment_info', 'mw_deliverydate_time'));

                    $shipment = Mage::getResourceModel('sales/order_shipment_collection')
                            ->addAttributeToSelect('*')
                            ->addAttributeToFilter('entity_id', $shipmentId)
                            ->getFirstItem();
                    //foreach ($shipments as $shipment) {
                        $assign = Mage::getModel("deliverymanagement/deliverymanagement");
                        $assign->setUser_id($userId);
                        $assign->setShipment_id($shipmentId);
                        $assign->setIncrement_id($shipment->getIncrement_id());
                        $assign->setIncrementId($shipment->getIncrement_id());
                        $assign->setCreated_at($shipment->getCreated_at());
                        $assign->setTotal_qty($shipment->getTotal_qty());
                        
                        $delivery = Mage::getModel('onestepcheckout/onestepcheckout')
                                ->getCollection()
                                ->addFieldToFilter('sales_order_id', $shipment->getOrder_id())
                                ->getFirstItem();
                        Mage::getSingleton('adminhtml/session')->addSuccess($delivery->getMw_onestepcheckout_date_id());
                        $assign->setMw_deliverydate_date($delivery->getMw_deliverydate_date());
                        $assign->setMw_deliverydate_time($delivery->getMw_deliverydate_time());
                        $assign->setMw_customercomment_info($delivery->getMw_customercomment_info());
                        
                        $assign->setAssigneddate(now());
                        $assign->setStatus("assigned");
                        $assign->setUpdate_at(now());
                        $assign->save();

                        $count++;
//                    }
                }
                Mage::getSingleton('adminhtml/session')->addSuccess('Total of ' . $count . ' shipment(s) were assigned.');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

}
