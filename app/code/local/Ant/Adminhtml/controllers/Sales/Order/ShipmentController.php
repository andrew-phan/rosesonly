<?php

include_once('Mage/Adminhtml/controllers/Sales/Order/ShipmentController.php');
class Ant_Adminhtml_Sales_Order_ShipmentController extends Mage_Adminhtml_Sales_Order_ShipmentController
{        
     protected function _initOrder() {
        $id = $this->getRequest()->getParam('order_id');
        $order = Mage::getModel('sales/order')->load($id);

        if (!$order->getId()) {
            $this->_getSession()->addError($this->__('This order no longer exists.'));
            $this->_redirect('*/*/');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        Mage::register('sales_order', $order);
        Mage::register('current_order', $order);
        return $order;
    }

    public function printmAction() {
        $this->printMessageCard();
    }

    public function printm2Action() {
        $this->printMessageCard('_2');
    }

    public function printm3Action() {
        $this->printMessageCard('_3');
    }

    private function printMessageCard($type=null){
        $order = $this->_initOrder();
        if (!empty($order)) {
            $order->setOrder($order);

            $_order = Mage::registry('current_order');
            $order_id = $_order->getId();
            $orders = Mage::getModel('onestepcheckout/onestepcheckout')->getCollection()->addFieldToFilter('sales_order_id', $order_id);

            foreach ($orders as $m_order) {
                $o = $m_order;
                $__order = Mage::getModel('onestepcheckout/onestepcheckout')->load($o->getId());
                $__order->setPrintMsg(true);
                $__order->save();
            }

            $pdf = Mage::getModel('Nastnet_OrderPrint/order_pdf_order')->getPdfm(array($order),$type);
            return $this->_prepareDownloadResponse('Message_' . $order->getIncrementId() . '_' . Mage::getSingleton('core/date')->date('YmdHis') . '.pdf', $pdf->render(), 'application/pdf');
        }
        $this->_redirect('*/*/');
    }
    public function doprintAction() { 
        $order = $this->_initOrder();
        if (!empty($order)) {
            $order->setOrder($order);

            $_order = Mage::registry('current_order');
            $order_id = $_order->getId();
            $orders = Mage::getModel('onestepcheckout/onestepcheckout')->getCollection()->addFieldToFilter('sales_order_id', $order_id);

            foreach ($orders as $m_order) {
                $o = $m_order;              
                $__order = Mage::getModel('onestepcheckout/onestepcheckout')->load($o->getId());
                $__order->setPrintDo(true);
                $__order->save();
            }

            return Mage::getModel('Ant_Messagecardsupport_Model_Order_Pdf_Shipment')->createPDF(array($order));
        }
    }
}
