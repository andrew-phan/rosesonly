<?php

class Ant_Fbintegration_IndexController extends Mage_Core_Controller_Front_Action {

    public function IndexAction() {

        $this->loadLayout();
        $this->getLayout()->getBlock("head")->setTitle($this->__("Titlename"));
        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
        $breadcrumbs->addCrumb("home", array(
            "label" => $this->__("Home Page"),
            "title" => $this->__("Home Page"),
            "link" => Mage::getBaseUrl()
        ));

        $breadcrumbs->addCrumb("titlename", array(
            "label" => $this->__("Titlename"),
            "title" => $this->__("Titlename")
        ));

        $this->renderLayout();
    }

    public function SaveFacebookFriendsAction() {

        $json_string = $_POST['friends'];

        $data = $json_string['data'];

        $this->saveFacebookFriends($data);
    }

    private function saveFacebookFriends($facebookFriends) {
        $customer = Mage::getSingleton('customer/session')->getCustomer();            
        foreach ($facebookFriends as $facebookFriend) {

            $collection_of_contact = Mage::getModel('fbintegration/contact')
                    ->getCollection();
            $collection_of_contact->addFieldToFilter('facebook_id', $facebookFriend['id']);
            
            if ($collection_of_contact->getFirstItem()->getData()) {
                //Mage::log($facebookFriend['name']." EXISTING.");
                continue;
            }
            
            //save a new one to database
            $contact = Mage::getModel('fbintegration/contact');
            
            $contact->setCustomerId($customer->getId());
            $contact->setFacebookId($facebookFriend['id']);
            $contact->setName($facebookFriend['name']);
            $contact->save();
            
            Mage::log("SAVED ID:" . $facebookFriend['id'] . " Name: " . $facebookFriend['name']);
        }
    }

}