<?php

class Ant_Latepayment_Adminhtml_LatepaymentController extends Mage_Adminhtml_Controller_Action {

    protected function _initAction() {
        $this->loadLayout()->_setActiveMenu("latepayment/latepayment")->_addBreadcrumb(Mage::helper("adminhtml")->__("Latepayment  Manager"), Mage::helper("adminhtml")->__("Latepayment Manager"));
        return $this;
    }

    public function indexAction() {
        $this->_initAction();
        $this->renderLayout();
    }

    public function reminderAction() {
        $Ids = $this->getRequest()->getParam('entity_id');      // $this->getMassactionBlock()->setFormFieldName('tax_id'); from Mage_Adminhtml_Block_Tax_Rate_Grid
        if (!is_array($Ids)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('tax')->__('Please select latepayment order.'));
        } else {
            try {
                foreach ($Ids as $Id) {
                    // Update datetime 
                    $item = Mage::getModel('latepayment/latepayment')->load($Id);
                    $item->setSent(now());
                    $item->save();
                    // Send email reminder
                    $this->sendReminder($item->getOrder_id());
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('tax')->__(
                                'Total of %d order(s) were remindered.', count($Ids)
                        )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function sendReminder($orderId) {
        $order = Mage::getModel('sales/order')->load($orderId);
        $email = $order->getCustomerEmail();
        //$email = 'hoang.dinh21@gmail.com';
        $mailSubject = 'Order #' . $order->increment_id . ' is late';

        $storeId = Mage::app()->getStore()->getId();
        $templateId = Mage::getStoreConfig('latepayment/latepaymentsetting/email_template', $storeId);
        echo $templateId;

        // Set sender information          
        $senderName = Mage::getStoreConfig('trans_email/ident_support/name');
        $senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');
        $sender = array('name' => $senderName, 'email' => $senderEmail);

        // Set variables that can be used in email template
        $vars = array(
            'order' => $order,
            'subject' => $mailSubject);

        // Send Transactional Email
        Mage::getModel('core/email_template')->setTemplateSubject($mailSubject)
                ->sendTransactional($templateId, $sender, $email, 'Admin', $vars, $storeId);

        //$translate  = Mage::getSingleton('core/translate');
        //$translate->setTranslateInline(true);
    }
    
    public function refeshAction(){
        $model = Mage::getModel('latepayment/cron');
        $model->getLatepayment();
        Mage::getSingleton('adminhtml/session')->addSuccess('Refesh successfully.');
        $this->_redirect('*/*/index');
    }
    


//    public function editAction() {
//        $brandsId = $this->getRequest()->getParam("id");
//        $brandsModel = Mage::getModel("sales/order")->load($brandsId);
//        if ($brandsModel->getId() || $brandsId == 0) {
//            Mage::register("latepayment_data", $brandsModel);
//            $this->loadLayout();
//            $this->_setActiveMenu("latepayment/latepayment");
//            $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Latepayment Manager"), Mage::helper("adminhtml")->__("Latepayment Manager"));
//            $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Latepayment Description"), Mage::helper("adminhtml")->__("Latepayment Description"));
//            $this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
//            $this->_addContent($this->getLayout()->createBlock("latepayment/adminhtml_latepayment_edit"))->_addLeft($this->getLayout()->createBlock("latepayment/adminhtml_latepayment_edit_tabs"));
//            $this->renderLayout();
//        } else {
//            Mage::getSingleton("adminhtml/session")->addError(Mage::helper("latepayment")->__("Item does not exist."));
//            $this->_redirect("*/*/");
//        }
//    }
//
//    public function newAction() {
//
//        $id = $this->getRequest()->getParam("id");
//        $model = Mage::getModel("latepayment/latepayment")->load($id);
//
//        $data = Mage::getSingleton("adminhtml/session")->getFormData(true);
//        if (!empty($data)) {
//            $model->setData($data);
//        }
//
//        Mage::register("latepayment_data", $model);
//
//        $this->loadLayout();
//        $this->_setActiveMenu("latepayment/latepayment");
//
//        $this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
//
//        $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Latepayment Manager"), Mage::helper("adminhtml")->__("Latepayment Manager"));
//        $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Latepayment Description"), Mage::helper("adminhtml")->__("Latepayment Description"));
//
//
//        $this->_addContent($this->getLayout()->createBlock("latepayment/adminhtml_latepayment_edit"))->_addLeft($this->getLayout()->createBlock("latepayment/adminhtml_latepayment_edit_tabs"));
//
//        $this->renderLayout();
//
//        // $this->_forward("edit");
//    }
//
//    public function saveAction() {
//
//        $post_data = $this->getRequest()->getPost();
//
//
//        if ($post_data) {
//
//            try {
//
//                $brandsModel = Mage::getModel("latepayment/latepayment")
//                        ->addData($post_data)
//                        ->setId($this->getRequest()->getParam("id"))
//                        ->save();
//
//                Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Latepayment was successfully saved"));
//                Mage::getSingleton("adminhtml/session")->setLatepaymentData(false);
//
//                if ($this->getRequest()->getParam("back")) {
//                    $this->_redirect("*/*/edit", array("id" => $brandsModel->getId()));
//                    return;
//                }
//                $this->_redirect("*/*/");
//                return;
//            } catch (Exception $e) {
//                Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
//                Mage::getSingleton("adminhtml/session")->setLatepaymentData($this->getRequest()->getPost());
//                $this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
//                return;
//            }
//        }
//        $this->_redirect("*/*/");
//    }
//
//    public function deleteAction() {
//        if ($this->getRequest()->getParam("id") > 0) {
//            try {
//                $brandsModel = Mage::getModel("latepayment/latepayment");
//                $brandsModel->setId($this->getRequest()->getParam("id"))->delete();
//                Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item was successfully deleted"));
//                $this->_redirect("*/*/");
//            } catch (Exception $e) {
//                Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
//                $this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
//            }
//        }
//        $this->_redirect("*/*/");
//    }
//    
}
