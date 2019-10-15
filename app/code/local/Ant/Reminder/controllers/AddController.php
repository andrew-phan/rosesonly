<?php

class Ant_Reminder_AddController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        if ($this->_request->isPost()) {
            return $this->indexPostAction();
        } else {
            $this->loadLayout();
            $this->_initLayoutMessages('customer/session');
            $this->getLayout()->getBlock("head")->setTitle($this->__("Add Reminder"));
            $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
            $breadcrumbs->addCrumb("home", array(
                "label" => $this->__("Home Page"),
                "title" => $this->__("Home Page"),
                "link" => Mage::getBaseUrl()
            ));

            $breadcrumbs->addCrumb("reminder", array(
                "label" => $this->__("Reminder"),
                "title" => $this->__("Reminder")
            ));
            $friends = Mage::getModel('fbintegration/contact')
                    ->getCollection()
                    ->addFieldToFilter('customer_id', $customer->getId());
            $this->getLayout()->getBlock('reminder_add')->assign('friends', $friends);
            $this->renderLayout();
        }
    }

    public function indexPostAction() {
        $occasion = $this->getRequest()->getParam('occasion');
        $title = $this->getRequest()->getParam('title');
        $name = $this->getRequest()->getParam('name');
        $gender = $this->getRequest()->getParam('gender');
        $dates = $this->getRequest()->getParam('dates');
        $address = $this->getRequest()->getParam('address');
        $notification_type = $this->getRequest()->getParam('notification_type');
        $gift_type = $this->getRequest()->getParam('gift_type');
        $cuctomer = Mage::getSingleton('customer/session')->getCustomer();
        $add = Mage::getModel('reminder/reminder');
        $add->setCustomerId($cuctomer->getId());
        $add->setOccasion($occasion);
        $add->setTitle($title);
        $add->setGender($gender);
        $add->setName($name);
        $add->setDate($dates);
        $add->setDeliveryAddress($address);
        $add->setNotificationType($notification_type);
        $add->setGiftType($gift_type);
        $add->save();
        $this->_redirect('reminder/manage', array('_secure' => true));
    }

}