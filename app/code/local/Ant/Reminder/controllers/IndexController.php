<?php

class Ant_Reminder_IndexController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        //$this->_redirect('reminder/list', array('_secure'=>true));	
        $this->loadLayout();
        $this->getLayout()->getBlock("head")->setTitle($this->__("Edit Reminder"));
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

        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $list = Mage::getModel('reminder/reminder')->getCollection()->addFieldToFilter('customer_id', $customer->getId());

        $this->getLayout()->getBlock('reminder_index')->assign('data', $list);
        $this->renderLayout();
    }

}