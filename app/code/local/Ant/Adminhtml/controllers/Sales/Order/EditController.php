<?php // include_once('Mage/Adminhtml/controllers/Sales/Order/EditController.php');
//class Ant_Adminhtml_Sales_Order_EditController extends Mage_Adminhtml_Sales_Order_EditController
//{
//    /**
//     * Start edit order initialization
//     */
//    public function startAction()
//    {
//        $this->_getSession()->clear();
//        $orderId = $this->getRequest()->getParam('order_id');
//        $order = Mage::getModel('sales/order')->load($orderId);
//        // Phuoc's code
//        Mage::getSingleton('customer/session')->setOldStatus($order->getStatus());
//        // end code
//        try {
//            if ($order->getId()) {
//                $this->_getSession()->setUseOldShippingMethod(true);
//                $this->_getOrderCreateModel()->initFromOrder($order);
//                $this->_redirect('*/*');
//            }
//            else {
//                $this->_redirect('*/sales_order/');
//            }
//        } catch (Mage_Core_Exception $e) {
//            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
//            $this->_redirect('*/sales_order/view', array('order_id' => $orderId));
//        } catch (Exception $e) {
//            Mage::getSingleton('adminhtml/session')->addException($e, $e->getMessage());
//            $this->_redirect('*/sales_order/view', array('order_id' => $orderId));
//        }
//    }
//}