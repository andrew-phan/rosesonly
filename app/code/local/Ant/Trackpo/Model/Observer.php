<?php

class Ant_Trackpo_Model_Observer {

    public function POCreated(Varien_Event_Observer $observer) {
        //Mage::dispatchEvent('admin_session_user_login_success', array('user'=>$user));
        //$user = $observer->getEvent()->getUser();
        //$user->doSomething();
        //Mage::dispatchEvent('erp_potrack_created', array('order' => $order));
        $event = $observer->getEvent();
        $order = $event->getOrder();
        $user = Mage::getSingleton('admin/session')->getUser();
        $message = "The Purchase order was created successfully by <b>".$user->getFirstName()." ".$user->getLastName()."</b>";
        $this->sendEmail($order, $message);
    }

    public function POModified(Varien_Event_Observer $observer) {
        //Mage::dispatchEvent('admin_session_user_login_success', array('user'=>$user));
        //$user = $observer->getEvent()->getUser();
        //$user->doSomething();
        //Mage::dispatchEvent('erp_potrack_modified', array('order' => $order));
        $event = $observer->getEvent();
        $order = $event->getOrder();
        $user = Mage::getSingleton('admin/session')->getUser();
        $message = "The Purchase order was modified by <b>".$user->getFirstName()." ".$user->getLastName()."</b>";
        $this->sendEmail($order, $message);
    }

    public function PODelete(Varien_Event_Observer $observer) {
        //Mage::dispatchEvent('erp_potrack_delete', array('order' => $order));
        $event = $observer->getEvent();
        $order = $event->getOrder();
        $user = Mage::getSingleton('admin/session')->getUser();
        $message = "The Purchase order was deleted by <b>".$user->getFirstName()." ".$user->getLastName()."</b>";
        $this->sendEmail($order, $message);
    }

    public function PODeliveried(Varien_Event_Observer $observer) {
        //Mage::dispatchEvent('erp_potrack_deliveried', array('order' => $order));
        $event = $observer->getEvent();
        $order = $event->getOrder();
        $message = "The Purchase order was deliveried";
        $this->sendEmail($order, $message);
    }

    public function sendEmail($order, $message) {
        $enable = Mage::getStoreConfig('trackposetting/trackpo/enable');
        $email = Mage::getStoreConfig('trackposetting/trackpo/email');
        $templateId = Mage::getStoreConfig('trackposetting/trackpo/template');

        if ($enable) {
            $mailSubject = 'Order #' . $order->increment_id . ' is canceled';

            // Set sender information          
            $senderName = Mage::getStoreConfig('trans_email/ident_support/name');
            $senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');
            $sender = array('name' => $senderName, 'email' => $senderEmail);

            // Set recepient information
            //$recepientEmail = 'john@example.com';
            //$recepientName = 'John Doe';       
            // Get Store ID    
            $storeId = Mage::app()->getStore()->getId();

            // Set variables that can be used in email template
            $supplier = $order->getSupplier();
            $supName = $supplier->getSup_name();
            $supMail = $supplier->getSup_mail();
            $vars = array(
                'poId' => $order->getPo_order_id(),
                'poDate' => $order->getPo_date(),
                'poSupplydate' => $order->getPo_supply_date(),
                'status' => $order->getStatuses(),
                'supName' => $supName,
                'supMail' => $supMail,
                'total' => $order->getProductTotal(),
                'message' => $message,
            );

            // Send Transactional Email
            Mage::getModel('core/email_template')->setTemplateSubject($mailSubject)
                    ->sendTransactional($templateId, $sender, $email, 'Admin', $vars, $storeId);

            //$translate  = Mage::getSingleton('core/translate');
            //$translate->setTranslateInline(true);
        }
    }

}
