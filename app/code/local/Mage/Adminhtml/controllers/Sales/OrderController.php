<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales orders controller
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Sales_OrderController extends Mage_Adminhtml_Controller_Action {

    /**
     * Array of actions which can be processed without secret key validation
     *
     * @var array
     */
    protected $_publicActions = array('view', 'index');

    /**
     * Additional initialization
     *
     */
    protected function _construct() {
        $this->setUsedModuleName('Mage_Sales');
    }

    /**
     * Init layout, menu and breadcrumb
     *
     * @return Mage_Adminhtml_Sales_OrderController
     */
    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('sales/order')
                ->_addBreadcrumb($this->__('Sales'), $this->__('Sales'))
                ->_addBreadcrumb($this->__('Orders'), $this->__('Orders'));
        return $this;
    }

    /**
     * Initialize order model instance
     *
     * @return Mage_Sales_Model_Order || false
     */
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

    /**
     * Orders grid
     */
    public function indexAction() {
        $this->_title($this->__('Sales'))->_title($this->__('Orders'));

        $this->_initAction()
                ->renderLayout();
    }

    /**
     * Order grid
     */
    public function gridAction() {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * View order detale
     */
    public function viewAction() {
        $this->_title($this->__('Sales'))->_title($this->__('Orders'));

        if ($order = $this->_initOrder()) {
            $this->_initAction();

            $this->_title(sprintf("#%s", $order->getRealOrderId()));

            $this->renderLayout();
        }
    }

    /**
     * Notify user
     */
    public function emailAction() {
        if ($order = $this->_initOrder()) {
            try {
                $order->sendNewOrderEmail();
                $historyItem = Mage::getResourceModel('sales/order_status_history_collection')
                        ->getUnnotifiedForInstance($order, Mage_Sales_Model_Order::HISTORY_ENTITY_NAME);
                if ($historyItem) {
                    $historyItem->setIsCustomerNotified(1);
                    $historyItem->save();
                }
                $this->_getSession()->addSuccess($this->__('The order email has been sent.'));
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError($this->__('Failed to send the order email.'));
                Mage::logException($e);
            }
        }
        $this->_redirect('*/sales_order/view', array('order_id' => $order->getId()));
    }

    /**
     * Cancel order
     */
    public function cancelAction() {
        if ($order = $this->_initOrder()) {
            try {
                $order->cancel()
                        ->save();
                $this->_getSession()->addSuccess(
                        $this->__('The order has been cancelled.')
                );
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError($this->__('The order has not been cancelled.'));
                Mage::logException($e);
            }
            $this->_redirect('*/sales_order/view', array('order_id' => $order->getId()));
        }
    }

    /**
     * Hold order
     */
    public function holdAction() {
        if ($order = $this->_initOrder()) {
            try {
                $order->hold()
                        ->save();
                $this->_getSession()->addSuccess(
                        $this->__('The order has been put on hold.')
                );
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError($this->__('The order was not put on hold.'));
            }
            $this->_redirect('*/sales_order/view', array('order_id' => $order->getId()));
        }
    }

    /**
     * Unhold order
     */
    public function unholdAction() {
        if ($order = $this->_initOrder()) {
            try {
                $order->unhold()
                        ->save();
                $this->_getSession()->addSuccess(
                        $this->__('The order has been released from holding status.')
                );
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError($this->__('The order was not unheld.'));
            }
            $this->_redirect('*/sales_order/view', array('order_id' => $order->getId()));
        }
    }

    /**
     * Manage payment state
     *
     * Either denies or approves a payment that is in "review" state
     */
    public function reviewPaymentAction() {
        try {
            if (!$order = $this->_initOrder()) {
                return;
            }
            $action = $this->getRequest()->getParam('action', '');
            switch ($action) {
                case 'accept':
                    $order->getPayment()->accept();
                    $message = $this->__('The payment has been accepted.');
                    break;
                case 'deny':
                    $order->getPayment()->deny();
                    $message = $this->__('The payment has been denied.');
                    break;
                case 'update':
                    $order->getPayment()
                            ->registerPaymentReviewAction(Mage_Sales_Model_Order_Payment::REVIEW_ACTION_UPDATE, true);
                    $message = $this->__('Payment update has been made.');
                    break;
                default:
                    throw new Exception(sprintf('Action "%s" is not supported.', $action));
            }
            $order->save();
            $this->_getSession()->addSuccess($message);
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('Failed to update the payment.'));
            Mage::logException($e);
        }
        $this->_redirect('*/sales_order/view', array('order_id' => $order->getId()));
    }

    /**
     * Add order comment action
     */
    public function addCommentAction() {
        if ($order = $this->_initOrder()) {
            try {
                $response = false;
                $data = $this->getRequest()->getPost('history');
                $notify = isset($data['is_customer_notified']) ? $data['is_customer_notified'] : false;
                $visible = isset($data['is_visible_on_front']) ? $data['is_visible_on_front'] : false;

                $order->addStatusHistoryComment($data['comment'], $data['status'])
                        ->setIsVisibleOnFront($visible)
                        ->setIsCustomerNotified($notify);

                $comment = trim(strip_tags($data['comment']));

                $order->save();
                $order->sendOrderUpdateEmail($notify, $comment);

                $this->loadLayout('empty');
                $this->renderLayout();
            } catch (Mage_Core_Exception $e) {
                $response = array(
                    'error' => true,
                    'message' => $e->getMessage(),
                );
            } catch (Exception $e) {
                $response = array(
                    'error' => true,
                    'message' => $this->__('Cannot add order history.')
                );
            }
            if (is_array($response)) {
                $response = Mage::helper('core')->jsonEncode($response);
                $this->getResponse()->setBody($response);
            }
        }
    }

    /**
     * Generate invoices grid for ajax request
     */
    public function invoicesAction() {
        $this->_initOrder();
        $this->getResponse()->setBody(
                $this->getLayout()->createBlock('adminhtml/sales_order_view_tab_invoices')->toHtml()
        );
    }

    /**
     * Generate shipments grid for ajax request
     */
    public function shipmentsAction() {
        $this->_initOrder();
        $this->getResponse()->setBody(
                $this->getLayout()->createBlock('adminhtml/sales_order_view_tab_shipments')->toHtml()
        );
    }

    /**
     * Generate creditmemos grid for ajax request
     */
    public function creditmemosAction() {
        $this->_initOrder();
        $this->getResponse()->setBody(
                $this->getLayout()->createBlock('adminhtml/sales_order_view_tab_creditmemos')->toHtml()
        );
    }

    /**
     * Generate order history for ajax request
     */
    public function commentsHistoryAction() {
        $this->_initOrder();
        $html = $this->getLayout()->createBlock('adminhtml/sales_order_view_tab_history')->toHtml();
        /* @var $translate Mage_Core_Model_Translate_Inline */
        $translate = Mage::getModel('core/translate_inline');
        if ($translate->isAllowed()) {
            $translate->processResponseBody($html);
        }
        $this->getResponse()->setBody($html);
    }

    /**
     * Cancel selected orders
     */
    public function massCancelAction() {
        $orderIds = $this->getRequest()->getPost('order_ids', array());
        $countCancelOrder = 0;
        $countNonCancelOrder = 0;
        foreach ($orderIds as $orderId) {
            $order = Mage::getModel('sales/order')->load($orderId);
            if ($order->canCancel()) {
                $order->cancel()
                        ->save();
                $countCancelOrder++;
            } else {
                $countNonCancelOrder++;
            }
        }
        if ($countNonCancelOrder) {
            if ($countCancelOrder) {
                $this->_getSession()->addError($this->__('%s order(s) cannot be canceled', $countNonCancelOrder));
            } else {
                $this->_getSession()->addError($this->__('The order(s) cannot be canceled'));
            }
        }
        if ($countCancelOrder) {
            $this->_getSession()->addSuccess($this->__('%s order(s) have been canceled.', $countCancelOrder));
        }
        $this->_redirect('*/*/');
    }

    /**
     * Hold selected orders
     */
    public function massHoldAction() {
        $orderIds = $this->getRequest()->getPost('order_ids', array());
        $countHoldOrder = 0;
        $countNonHoldOrder = 0;
        foreach ($orderIds as $orderId) {
            $order = Mage::getModel('sales/order')->load($orderId);
            if ($order->canHold()) {
                $order->hold()
                        ->save();
                $countHoldOrder++;
            } else {
                $countNonHoldOrder++;
            }
        }
        if ($countNonHoldOrder) {
            if ($countHoldOrder) {
                $this->_getSession()->addError($this->__('%s order(s) were not put on hold.', $countNonHoldOrder));
            } else {
                $this->_getSession()->addError($this->__('No order(s) were put on hold.'));
            }
        }
        if ($countHoldOrder) {
            $this->_getSession()->addSuccess($this->__('%s order(s) have been put on hold.', $countHoldOrder));
        }

        $this->_redirect('*/*/');
    }

    /**
     * Unhold selected orders
     */
    public function massUnholdAction() {
        $orderIds = $this->getRequest()->getPost('order_ids', array());
        $countUnholdOrder = 0;
        $countNonUnholdOrder = 0;

        foreach ($orderIds as $orderId) {
            $order = Mage::getModel('sales/order')->load($orderId);
            if ($order->canUnhold()) {
                $order->unhold()
                        ->save();
                $countUnholdOrder++;
            } else {
                $countNonUnholdOrder++;
            }
        }
        if ($countNonUnholdOrder) {
            if ($countUnholdOrder) {
                $this->_getSession()->addError($this->__('%s order(s) were not released from holding status.', $countNonUnholdOrder));
            } else {
                $this->_getSession()->addError($this->__('No order(s) were released from holding status.'));
            }
        }
        if ($countUnholdOrder) {
            $this->_getSession()->addSuccess($this->__('%s order(s) have been released from holding status.', $countUnholdOrder));
        }
        $this->_redirect('*/*/');
    }

    /**
     * Change status for selected orders
     */
    public function massStatusAction() {
        
    }

    /**
     * Print documents for selected orders
     */
    public function massPrintAction() {
        $orderIds = $this->getRequest()->getPost('order_ids');
        $document = $this->getRequest()->getPost('document');
    }

    /**
     * Print invoices for selected orders
     */
    public function pdfinvoicesAction() {
        $orderIds = $this->getRequest()->getPost('order_ids');
        $flag = false;
        if (!empty($orderIds)) {
            $orders = array();
            foreach ($orderIds as $orderId) {
                /*
                $invoices = Mage::getResourceModel('sales/order_invoice_collection')
                        ->setOrderFilter($orderId)
                        ->load();
                if ($invoices->getSize() > 0) {
                    $flag = true;
                    if (!isset($pdf)) {
                        $pdf = Mage::getModel('sales/order_pdf_invoice')->getPdf($invoices);
                    } else {
                        $pages = Mage::getModel('sales/order_pdf_invoice')->getPdf($invoices);
                        $pdf->pages = array_merge($pdf->pages, $pages->pages);
                    }
                }
                 * 
                 */
                $flag = true;
                $order = Mage::getModel('sales/order')->load($orderId);
                $orders[] = $order;
            }
            
            if ($flag) {
                //return $this->_prepareDownloadResponse(
                //                'invoice' . Mage::getSingleton('core/date')->date('Y-m-d_H-i-s') . '.pdf', $pdf->render(), 'application/pdf'
                //);
                return Mage::getModel('Nastnet_OrderPrint/order_pdf_order')->createPDF($orders);
            } else {
                $this->_getSession()->addError($this->__('There are no printable documents related to selected orders.'));
                $this->_redirect('*/*/');
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Print shipments for selected orders
     */
    public function pdfshipmentsAction() {
        $orderIds = $this->getRequest()->getPost('order_ids');
        $flag = false;
        if (!empty($orderIds)) {
            foreach ($orderIds as $orderId) {
                $shipments = Mage::getResourceModel('sales/order_shipment_collection')
                        ->setOrderFilter($orderId)
                        ->load();
                if ($shipments->getSize()) {
                    $flag = true;
                    if (!isset($pdf)) {
                        $pdf = Mage::getModel('sales/order_pdf_shipment')->getPdf($shipments);
                    } else {
                        $pages = Mage::getModel('sales/order_pdf_shipment')->getPdf($shipments);
                        $pdf->pages = array_merge($pdf->pages, $pages->pages);
                    }
                }
            }
            if ($flag) {
                return $this->_prepareDownloadResponse(
                                'packingslip' . Mage::getSingleton('core/date')->date('Y-m-d_H-i-s') . '.pdf', $pdf->render(), 'application/pdf'
                );
            } else {
                $this->_getSession()->addError($this->__('There are no printable documents related to selected orders.'));
                $this->_redirect('*/*/');
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Print creditmemos for selected orders
     */
    public function pdfcreditmemosAction() {
        $orderIds = $this->getRequest()->getPost('order_ids');
        $flag = false;
        if (!empty($orderIds)) {
            foreach ($orderIds as $orderId) {
                $creditmemos = Mage::getResourceModel('sales/order_creditmemo_collection')
                        ->setOrderFilter($orderId)
                        ->load();
                if ($creditmemos->getSize()) {
                    $flag = true;
                    if (!isset($pdf)) {
                        $pdf = Mage::getModel('sales/order_pdf_creditmemo')->getPdf($creditmemos);
                    } else {
                        $pages = Mage::getModel('sales/order_pdf_creditmemo')->getPdf($creditmemos);
                        $pdf->pages = array_merge($pdf->pages, $pages->pages);
                    }
                }
            }
            if ($flag) {
                return $this->_prepareDownloadResponse(
                                'creditmemo' . Mage::getSingleton('core/date')->date('Y-m-d_H-i-s') . '.pdf', $pdf->render(), 'application/pdf'
                );
            } else {
                $this->_getSession()->addError($this->__('There are no printable documents related to selected orders.'));
                $this->_redirect('*/*/');
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Print all documents for selected orders
     */
    public function pdfdocsAction() {
        $orderIds = $this->getRequest()->getPost('order_ids');
        $flag = false;
        if (!empty($orderIds)) {
            foreach ($orderIds as $orderId) {
                $invoices = Mage::getResourceModel('sales/order_invoice_collection')
                        ->setOrderFilter($orderId)
                        ->load();
                if ($invoices->getSize()) {
                    $flag = true;
                    if (!isset($pdf)) {
                        $pdf = Mage::getModel('sales/order_pdf_invoice')->getPdf($invoices);
                    } else {
                        $pages = Mage::getModel('sales/order_pdf_invoice')->getPdf($invoices);
                        $pdf->pages = array_merge($pdf->pages, $pages->pages);
                    }
                }

                $shipments = Mage::getResourceModel('sales/order_shipment_collection')
                        ->setOrderFilter($orderId)
                        ->load();
                if ($shipments->getSize()) {
                    $flag = true;
                    if (!isset($pdf)) {
                        $pdf = Mage::getModel('sales/order_pdf_shipment')->getPdf($shipments);
                    } else {
                        $pages = Mage::getModel('sales/order_pdf_shipment')->getPdf($shipments);
                        $pdf->pages = array_merge($pdf->pages, $pages->pages);
                    }
                }

                $creditmemos = Mage::getResourceModel('sales/order_creditmemo_collection')
                        ->setOrderFilter($orderId)
                        ->load();
                if ($creditmemos->getSize()) {
                    $flag = true;
                    if (!isset($pdf)) {
                        $pdf = Mage::getModel('sales/order_pdf_creditmemo')->getPdf($creditmemos);
                    } else {
                        $pages = Mage::getModel('sales/order_pdf_creditmemo')->getPdf($creditmemos);
                        $pdf->pages = array_merge($pdf->pages, $pages->pages);
                    }
                }
            }
            if ($flag) {
                return $this->_prepareDownloadResponse(
                                'docs' . Mage::getSingleton('core/date')->date('Y-m-d_H-i-s') . '.pdf', $pdf->render(), 'application/pdf'
                );
            } else {
                $this->_getSession()->addError($this->__('There are no printable documents related to selected orders.'));
                $this->_redirect('*/*/');
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Atempt to void the order payment
     */
    public function voidPaymentAction() {
        if (!$order = $this->_initOrder()) {
            return;
        }
        try {
            $order->getPayment()->void(
                    new Varien_Object() // workaround for backwards compatibility
            );
            $order->save();
            $this->_getSession()->addSuccess($this->__('The payment has been voided.'));
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('Failed to void the payment.'));
            Mage::logException($e);
        }
        $this->_redirect('*/*/view', array('order_id' => $order->getId()));
    }

    /**
     * Acl check for admin
     *
     * @return bool
     */
    protected function _isAllowed() {
        $action = strtolower($this->getRequest()->getActionName());
        switch ($action) {
            case 'hold':
                $aclResource = 'sales/order/actions/hold';
                break;
            case 'unhold':
                $aclResource = 'sales/order/actions/unhold';
                break;
            case 'email':
                $aclResource = 'sales/order/actions/email';
                break;
            case 'cancel':
                $aclResource = 'sales/order/actions/cancel';
                break;
            case 'view':
                $aclResource = 'sales/order/actions/view';
                break;
            case 'addcomment':
                $aclResource = 'sales/order/actions/comment';
                break;
            case 'creditmemos':
                $aclResource = 'sales/order/actions/creditmemo';
                break;
            case 'reviewpayment':
                $aclResource = 'sales/order/actions/review_payment';
                break;
            default:
                $aclResource = 'sales/order';
                break;
        }
        return Mage::getSingleton('admin/session')->isAllowed($aclResource);
    }

    /**
     * Export order grid to CSV format
     */
    public function exportCsvAction() {
        $fileName = 'orders.csv';
        $grid = $this->getLayout()->createBlock('adminhtml/sales_order_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     *  Export order grid to Excel XML format
     */
    public function exportExcelAction() {
        $fileName = 'orders.xml';
        $grid = $this->getLayout()->createBlock('adminhtml/sales_order_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }

    /**
     * Order transactions grid ajax action
     *
     */
    public function transactionsAction() {
        $this->_initOrder();
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * Edit order address form
     */
    public function addressAction() {
        $addressId = $this->getRequest()->getParam('address_id');
        $address = Mage::getModel('sales/order_address')
                ->getCollection()
                ->addFilter('entity_id', $addressId)
                ->getItemById($addressId);
        if ($address) {
            Mage::register('order_address', $address);
            $this->loadLayout();
            // Do not display VAT validation button on edit order address form
            $addressFormContainer = $this->getLayout()->getBlock('sales_order_address.form.container');
            if ($addressFormContainer) {
                $addressFormContainer->getChild('form')->setDisplayVatValidationButton(false);
            }

            $this->renderLayout();
        } else {
            $this->_redirect('*/*/');
        }
    }

    /**
     * Save order address
     */
    public function addressSaveAction() {
        $addressId = $this->getRequest()->getParam('address_id');
        $address = Mage::getModel('sales/order_address')->load($addressId);
        $data = $this->getRequest()->getPost();
        if ($data && $address->getId()) {
            $address->addData($data);
            try {
                $address->implodeStreetAddress()
                        ->save();
                $this->_getSession()->addSuccess(Mage::helper('sales')->__('The order address has been updated.'));
                $this->_redirect('*/*/view', array('order_id' => $address->getParentId()));
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException(
                        $e, Mage::helper('sales')->__('An error occurred while updating the order address. The address has not been changed.')
                );
            }
            $this->_redirect('*/*/address', array('address_id' => $address->getId()));
        } else {
            $this->_redirect('*/*/');
        }
    }

    public function printmAction() {
        $this->printMessage();
    }

    public function printm2Action() {
        $this->printMessage('_2');
    }

    public function printm3Action() {
        $this->printMessage('_3');
    }

    private function printMessage($type=''){
        $order = $this->_initOrder();
        if (!empty($order)) {
            $order->setOrder($order);

            $_order = Mage::registry('current_order');
            $order_id = $_order->getId();
            $orders = Mage::getModel('onestepcheckout/onestepcheckout')->getCollection()->addFieldToFilter('sales_order_id', $order_id);

            foreach ($orders as $m_order) { //$orders chua nhieu doi tuong. va` chi lay dc doi tuong con qua foreach
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
    public function soprintAction() {
        $order = $this->_initOrder();
        if (!empty($order)) {
            $order->setOrder($order);
            return Mage::getModel('Nastnet_OrderPrint/order_pdf_order')->createPDF(array($order));
        }
    }

    public function doprintAction() {
        $order = $this->_initOrder();
        if (!empty($order)) {
            $order->setOrder($order);

            /* Hoang add */
            $_order = Mage::registry('current_order');
            $orderid = $_order->getId();
            $orders = Mage::getModel('onestepcheckout/onestepcheckout')->getCollection()->addFieldToFilter('sales_order_id', $orderid);
            $o = '';

            foreach ($orders as $m_order) { //$orders chua nhieu doi tuong. va` chi lay dc doi tuong con qua foreach
                $o = $m_order;               
                $__order = Mage::getModel('onestepcheckout/onestepcheckout')->load($o->getId());
                $__order->setPrintDo(true);
                $__order->save();
            }
            

            return Mage::getModel('Ant_Messagecardsupport_Model_Order_Pdf_Shipment')->createPDF(array($order));
        }
    }

    public function emailFEFAction() {
        $order = $this->_initOrder();
        if (!empty($order)) {
            $order->setOrder($order);

            $fef_id = $this->getRequest()->getPost('fef_id');
            //$delivery_time = $this->getRequest()->getPost('delivery_time');

            $mail_to = $this->getRequest()->getPost('mail_to');
            $cc = $this->getRequest()->getPost('cc');
            $bcc = $this->getRequest()->getPost('bcc');

            $to_name = $this->getRequest()->getPost('to_name');

            $template = $this->getRequest()->getPost('template');
            $template = $this->getContent($template, $order, $to_name, $fef_id);

            $mail_subject = $this->getRequest()->getPost('mail_subject');
            $mail_subject = $this->getSubject($mail_subject, $order);

            $from_mail = $this->getRequest()->getPost('from_mail');
            $from_name = $this->getRequest()->getPost('from_name');

            $mail = new Zend_Mail();
            $mail->setBodyHtml($template);
            $mail->setSubject($mail_subject);
            $mail->setFrom($from_mail, $from_name);
            //$mail->setType('html'); // YOu can use Html or text as Mail format

            $mail_list = explode(',', $mail_to);
            $cc_list = explode(',', $cc);
            $bcc_list = explode(',', $bcc);
            
            $mail->addTo($mail_list);
            $mail->addCc($cc_list);
            $mail->addBcc($bcc_list);
            $transport = Mage::helper('smtppro')->getTransport();
            try {
                $mail->send($transport);
            } catch (Exception $e) {
                $response = array(
                    'error' => true,
                    'message' => $e,
                );
                if (is_array($response)) {
                    $response = Mage::helper('core')->jsonEncode($response);
                    $this->getResponse()->setBody($response);
                }
            }

            $response = array(
                'error' => false,
                'message' => $this->__('Your request has been sent'),
            );

            if (is_array($response)) {
                $response = Mage::helper('core')->jsonEncode($response);
                $this->getResponse()->setBody($response);
            }
        }
    }

    function getSubject($subject, $order) {
        if (!empty($order)) {
            $SO_ID = $order->getRealOrderId();
            /* Hoang add */
            $orders = Mage::getModel('onestepcheckout/onestepcheckout')->getCollection()->addFieldToFilter('sales_order_id', $order->getId());
            $o = '';
            foreach ($orders as $m_order) { //$orders chua nhieu doi tuong. va` chi lay dc doi tuong con qua foreach
                $o = $m_order;
            }
            /* end */

            $DELIVERY_DATE = Mage::getModel('core/date')->date('d-m-Y', strtotime(
                            $o->getMwDeliverydateDate()));
            $DELIVERY_TIME = $o->getMwDeliverydateTime();
            

            $subject = str_replace("##SO_ID##", $SO_ID, $subject);
            $subject = str_replace("##DELIVERY_DATE##", $DELIVERY_DATE, $subject);
            $subject = str_replace("##DELIVERY_TIME##", $DELIVERY_TIME, $subject);
        }
        return $subject;
    }

    function getContent($template, $order, $toname, $fef_id) {
        if (!empty($order)) {
            $TO_NAME = $toname;
            //$CUSTOMER_PHONE = '';
            $FEF_ID = $fef_id;           
            $SO_ID = $order->getRealOrderId();
            $s_address = $order->getShippingAddress();
            $RECEIPT_NAME .= $s_address->getData('firstname') . ' ' . $s_address->getData('lastname');

            //print company
            $sent_to_address .= $s_address->getCompany() . '<br/>';
            
            //print address
            $sent_to_address .= preg_replace("/[\n\r]/", "<br/>", $s_address->getData('street')).'<br/>';

            $sent_countryName = Mage::getModel('directory/country')->load($s_address->getCountry())->getName();
            $sent_to_address .= $sent_countryName . ' ' . $s_address->getData('postcode');

            //print telephone
            $RECEIPT_PHONE .= 'T:' . $s_address->getData('telephone');

            $RECEIPT_ADDRESS = $sent_to_address;


            $html .='';
            foreach ($order->getAllItems() as $item) {
                if ($item->getParentItem()) {
                    $product = Mage::getModel('catalog/product')->load($item->getProductId());
                    if ($product->getAdditional() != 1) {
                        $html .='<br/>Add on:' . $item->getName() . '<br/>';
                        continue;
                    }
                } else {
                    if ($item->getProductType() == 'simple') {
                        $html .= '<br/><br/><font color=red>' . $item->getName() . '</font><br/>';
                    } else {
                        $html .= '<br/>' . $item->getSku() . '<br/>';
                        $html .= $item->getName() . '<br/>';
                    }
                    $options = $this->getItemOptions($item);
                    if ($options) {
                        foreach ($options as $option) {
                            // draw options label
                            $html .= '<br/>' . $option['label'];

                            if ($option['value']) {
                                if (isset($option['print_value'])) {
                                    $_printValue = $option['print_value'];
                                } else {
                                    $_printValue = strip_tags($option['value']);
                                }
                                $values = explode(', ', $_printValue);
                                foreach ($values as $value) {
                                    $html .= ' ' . $value;
                                }
                            }
                        }
                    }
                }
            }


            $ORDER_INFO = $html;


            /* Hoang add */

            $orders = Mage::getModel('onestepcheckout/onestepcheckout')->getCollection()->addFieldToFilter('sales_order_id', $order->getId());
            $o = '';

            foreach ($orders as $m_order) { //$orders chua nhieu doi tuong. va` chi lay dc doi tuong con qua foreach
                $o = $m_order;
            }
            /* end */

            $DELIVERY_DATE = Mage::getModel('core/date')->date('d-m-Y', strtotime(
                            $o->getMwDeliverydateDate()));
            $DELIVERY_TIME = $o->getMwDeliverydateTime();

            $MESSAGE = '';
            $message = Mage::getModel('giftmessage/message');
            $gift_message_id = $order->getGiftMessageId();

            if (!is_null($gift_message_id)) {
                $message->load((int) $gift_message_id);
                $MESSAGE = $message->getData('message');
            }

            $template = str_replace("##TO_NAME##", $TO_NAME, $template);
            $template = str_replace("##DELIVERY_TIME##", $DELIVERY_TIME, $template);
            $template = str_replace("##DELIVERY_DATE##", $DELIVERY_DATE, $template);
            $template = str_replace("##RECEIPT_NAME##", $RECEIPT_NAME, $template);
            $template = str_replace("##RECEIPT_ADDRESS##", $RECEIPT_ADDRESS, $template);
            $template = str_replace("##RECEIPT_PHONE##", $RECEIPT_PHONE, $template);
            $template = str_replace("##FEF_PRODUCT_ID##", $FEF_ID, $template);
            $template = str_replace("##SO_ID##", $SO_ID, $template);
            $template = str_replace("##ORDER_INFO##", $ORDER_INFO, $template);
        }

        return $template;
    }

    public function getItemOptions($item) {
        $result = array();
        if ($options = $item->getProductOptions()) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }
        }
        return $result;
    }

    public function previewFEFAction() {
        $order = $this->_initOrder();
        if (!empty($order)) {
            $order->setOrder($order);
            $fef_id = $this->getRequest()->getPost('fef_id');
            $delivery_time = $this->getRequest()->getPost('delivery_time');

            $to_name = $this->getRequest()->getPost('to_name');

            $template = $this->getRequest()->getPost('template');
            $template = $this->getContent($template, $order, $to_name, $fef_id, $delivery_time);

            $response = array(
                'error' => false,
                'template' => $template
            );

            if (is_array($response)) {
                $response = Mage::helper('core')->jsonEncode($response);
                $this->getResponse()->setBody($response);
            }
        }
    }
    
    /**
     * Order id param name
     */
    const PARAM_ORDER_ID = 'order_id';

    /**
     * UnCancel order action
     */
    public function unCancelAction()
    {
        $orderId = $this->getRequest()->getParam(self::PARAM_ORDER_ID);
        $isOrderUnCanceled = Mage::helper('magebeam_getorderback')->unCancelOrder($orderId);
        if ($isOrderUnCanceled) {
            $this->_getSession()->addSuccess(
                $this->__('The order has been uncancelled.')
            );
        } else {
            $this->_getSession()->addError(
                Mage::helper('sales')->__('This order no longer exists.')
            );
        }
        $this->_redirect('*/sales_order/index');
    }
}
