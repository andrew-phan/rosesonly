<?php
include_once('Mage/Adminhtml/controllers/Sales/Order/CreateController.php');
class Ant_Adminhtml_Sales_Order_CreateController extends Mage_Adminhtml_Sales_Order_CreateController
{
    public function saveAction()
    {
        try {
            $this->_processActionData('save');
            if ($paymentData = $this->getRequest()->getPost('payment')) {
                $this->_getOrderCreateModel()->setPaymentData($paymentData);
                $this->_getOrderCreateModel()->getQuote()->getPayment()->addData($paymentData);
            }

            $sale_order_create = $this->_getOrderCreateModel();
            
            $order = $sale_order_create
                ->setIsValidate(true)
                ->importPostData($this->getRequest()->getPost('order'))
                ->createOrder();
            ///// Phuoc's code: set order status offline
            $order->setStatus('pending_offline');
            
            // Hau Vo: set value of is_draft
            $is_draft = $this->getRequest()->getPost('is_draft');
            $order->setData('is_draft', $is_draft);
            $order->save();
            
            $delivery = Mage::getModel('onestepcheckout/onestepcheckout');
            $delivery->setSales_order_id($order->getId());
            $comment = $this->getRequest()->getPost('mw_customercomment_info');
            
            $date = $this->getRequest()->getPost('_deliverydate');
            $del_date = new DateTime();
            $del_date = $del_date->createFromFormat('d/m/Y',$date);
            
            //$today = new DateTime();
            //if($del_date == $today->format('m/d/Y'))
            $del_time = $this->getRequest()->getPost('mw_deliverydate_time_today');
            //else $del_time = $this->getRequest()->getPost('mw_deliverydate_time');

            $delivery->setMw_customercomment_info($comment);
            $delivery->setMw_deliverydate_date($del_date->format('Y-m-d'));
            $delivery->setMw_deliverydate_time($del_time);
            $delivery->save();
            /////////////////////////////////////////////
            if ($sale_order_create->getSendConfirmation()) {
                if($is_draft!=='1')
                    $order->sendNewOrderEmail();
            }
            
            $this->_getSession()->clear();
            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The order has been created.'));
            $this->_redirect('*/sales_order/view', array('order_id' => $order->getId()));
        } catch (Mage_Payment_Model_Info_Exception $e) {
            $this->_getOrderCreateModel()->saveQuote();
            $message = $e->getMessage();
            if( !empty($message) ) {
                $this->_getSession()->addError($message);
            }
            $this->_redirect('*/*/');
        } catch (Mage_Core_Exception $e){
            $message = $e->getMessage();
            if( !empty($message) ) {
                $this->_getSession()->addError($message);
            }
            $this->_redirect('*/*/');
        }
        catch (Exception $e){
            $this->_getSession()->addException($e, $this->__('Order saving error: %s', $e->getMessage()));
            $this->_redirect('*/*/');
        }
    }
    
}