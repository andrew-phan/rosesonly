<?php
/**
 * Adminhtml sales order edit controller
 *
 * @category   
 * @package    Ant_HHonors_Adminhtml
 * @author     kydrenw@antking.com.vn
 */
include_once("Mage/Adminhtml/controllers/Sales/Order/InvoiceController.php");
class Ant_Adminhtml_Sales_Order_InvoiceController extends Mage_Adminhtml_Sales_Order_InvoiceController
{
 
     /**
     * Save invoice
     * We can save only new invoice. Existing invoices are not editable
     */
    public function saveAction()
    {
        $data = $this->getRequest()->getPost('invoice');
        $orderId = $this->getRequest()->getParam('order_id');

        if (!empty($data['comment_text'])) {
            Mage::getSingleton('adminhtml/session')->setCommentText($data['comment_text']);
        }

        try {
            $invoice = $this->_initInvoice();
            if ($invoice) {

                if (!empty($data['capture_case'])) {
                    $invoice->setRequestedCaptureCase($data['capture_case']);
                }

                if (!empty($data['comment_text'])) {
                    $invoice->addComment(
                        $data['comment_text'],
                        isset($data['comment_customer_notify']),
                        isset($data['is_visible_on_front'])
                    );
                }

                $invoice->register();

                if (!empty($data['send_email'])) {
                    $invoice->setEmailSent(true);
                }

                $invoice->getOrder()->setCustomerNoteNotify(!empty($data['send_email']));
                $invoice->getOrder()->setIsInProcess(true);

                $transactionSave = Mage::getModel('core/resource_transaction')
                    ->addObject($invoice)
                    ->addObject($invoice->getOrder());
                $shipment = false;
                if (!empty($data['do_shipment']) || (int) $invoice->getOrder()->getForcedDoShipmentWithInvoice()) {
                    $shipment = $this->_prepareShipment($invoice);
                    if ($shipment) {
                        $shipment->setEmailSent($invoice->getEmailSent());
                        $transactionSave->addObject($shipment);
                    }
                }
                $transactionSave->save();

                if (isset($shippingResponse) && $shippingResponse->hasErrors()) {
                    $this->_getSession()->addError($this->__('The invoice and the shipment  have been created. The shipping label cannot be created at the moment.'));
                } elseif (!empty($data['do_shipment'])) {
                    $this->_getSession()->addSuccess($this->__('The invoice and shipment have been created.'));
                } else {
                    $this->_getSession()->addSuccess($this->__('The invoice has been created.'));
                }

                // send invoice/shipment emails
                $comment = '';
                if (isset($data['comment_customer_notify'])) {
                    $comment = $data['comment_text'];
                }
                try {
                    $invoice->sendEmail(!empty($data['send_email']), $comment);
                } catch (Exception $e) {
                    Mage::logException($e);
                    $this->_getSession()->addError($this->__('Unable to send the invoice email.'));
                }
                if ($shipment) {
                    try {
                        $shipment->sendEmail(!empty($data['send_email']));
                    } catch (Exception $e) {
                        Mage::logException($e);
                        $this->_getSession()->addError($this->__('Unable to send the shipment email.'));
                    }
                }
                Mage::getSingleton('adminhtml/session')->getCommentText(true);
                
                /*generate dat file*/
                $current_order = $invoice->getOrder();
                date_default_timezone_set('Asia/Singapore');
                $hhonors_number = $current_order->getData('order_hhonors_number');
                if($hhonors_number){
                    //$hhonors_bonus_code = $current_order->getData('order_hhonors_bonus_code');
					$hhonors_bonus_code = 'RSES';
                    $lastname = $current_order->getBillingAddress()->getData('lastname');
                    $juliandate = (int)(date('Y')/100).strftime('%y%j',strtotime($current_order->getCreatedAt()));
                    $time_created = new DateTime ($current_order->getCreatedAt(), new DateTimeZone('Asia/Singapore'));
                    $date = $time_created->format("Ymd");
                    $time = $time_created->format("His");
                    $file_name = "ROSE".$date."_".$time.".dat";
                    $content  = '0'.'ROSE'.'REQ'.'HH'.$juliandate.$time.PHP_EOL
                                .'1'.'T'.'ROSE'.'TOHH'.$juliandate.PHP_EOL;
                    $items = $current_order->getAllVisibleItems();
                    $count = 0;
                    foreach($items as $index=>$item) {
                        $pid = $item->getProductId();
                        $_mproduct = Mage::getModel('catalog/product')->load($pid);
                        if($_mproduct->getTypeId() == "bundle"){
                            $count ++;
                            $content .= '2'.str_pad($current_order->getIncrementId(),16,"0",STR_PAD_LEFT).
					str_pad($hhonors_number,9,"0",STR_PAD_LEFT).
					str_pad($lastname,20," ",STR_PAD_RIGHT).					
					str_repeat(' ', 19).
                            		str_pad((int) 1000,7,"0",STR_PAD_LEFT).'99'.
					substr($_mproduct->getSku(),0,2).
					str_pad($hhonors_bonus_code,6," ",STR_PAD_RIGHT).PHP_EOL;
                        }
                    }   
                    $content .='9'.str_pad($count,9,"0",STR_PAD_LEFT).str_pad((int) 1000,11,"0",STR_PAD_LEFT).PHP_EOL
                               .'9'.str_pad($count+4,9,"0",STR_PAD_LEFT);
                    $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/dat/".$file_name,"wb");
                    fwrite($fp,$content);
                    fclose($fp);
                }
                /*end generate dat file*/
                    
                $this->_redirect('*/sales_order/view', array('order_id' => $orderId));
            } else {
                $this->_redirect('*/*/new', array('order_id' => $orderId));
            }
            return;
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('Unable to save the invoice.'));
            Mage::logException($e);
        }
        $this->_redirect('*/*/new', array('order_id' => $orderId));
    }
}
