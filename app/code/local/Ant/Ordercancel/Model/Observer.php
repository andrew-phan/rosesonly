<?php

class Ant_Ordercancel_Model_Observer {

    public function sendEmailtoAdmin(Varien_Event_Observer $observer) {
        //Mage::dispatchEvent('admin_session_user_login_success', array('user'=>$user));
        //$user = $observer->getEvent()->getUser();
        //$user->doSomething();

        $enable = Mage::getStoreConfig('cancel_order_notification/cancel_order_email_notification/cancel_order_enable');
        $email = Mage::getStoreConfig('cancel_order_notification/cancel_order_email_notification/cancel_order_email');

        if ($enable) {
            $event = $observer->getEvent();
            $order = $event->getOrder();

            $mailSubject = 'Order #' . $order->increment_id . ' is canceled';

            // Transactional Email Template's ID
            $templateId = 'cancel_order_email_template';

            // Set sender information          
            $senderName = Mage::getStoreConfig('trans_email/ident_support/name');
            $senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');
            $sender = array('name' => $senderName, 'email' => $senderEmail);

            // Set recepient information
            //$recepientEmail = 'john@example.com';
            //$recepientName = 'John Doe';       
            // Get Store ID    
            $store = Mage::app()->getStore()->getId();

            // Set variables that can be used in email template
            $vars = array(
                'order' => $order,
                'orderUrl' => '',
                'customerUrl' => '',
            );

            // Send Transactional Email
            Mage::getModel('core/email_template')->setTemplateSubject($mailSubject)
                    ->sendTransactional($templateId, $sender, $email, 'Admin', $vars, $storeId);

            //$translate  = Mage::getSingleton('core/translate');
            //$translate->setTranslateInline(true);
        }
    }

}
