<?php

class Ant_Latepayment_Block_Adminhtml_Latepayment_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {

        $form = new Varien_Data_Form();
        $this->setForm($form);
        $Orderfieldset = $form->addFieldset("orderinfo_form", array("legend" => Mage::helper("latepayment")->__("Order information")));
        $Orderfieldset->addField("increment_id", "label", array(
            "label" => Mage::helper("latepayment")->__("Order #"),
            "class" => "required-entry",
            "required" => false,
            "name" => "increment_id",
        ));
        $Orderfieldset->addField("created_at", "label", array(
            "label" => Mage::helper("latepayment")->__("Order Date"),
            //"class" => "required-entry",
            "required" => false,
            "name" => "create_at",
        ));
        $Orderfieldset->addField("status", "label", array(
            "label" => Mage::helper("latepayment")->__("Order status"),
            //"class" => "required-entry",
            "required" => false,
            "name" => "status",
        ));

        $Customerfieldset = $form->addFieldset("customer_form", array("legend" => Mage::helper("latepayment")->__("Customer information")));
        $Customerfieldset->addField("customer_id", "label", array(
            "label" => Mage::helper("latepayment")->__("Customer #"),
            "class" => "required-entry",
            "required" => false,
            "name" => "customer_id",
        ));
        $Customerfieldset->addField("customer_name", "label", array(
            "label" => Mage::helper("latepayment")->__("Customer name"),
            //"class" => "required-entry",
            "required" => false,
            "name" => "shipping_name",
        ));

        $Emailfieldset = $form->addFieldset("email_form", array("legend" => Mage::helper("latepayment")->__("Email reminder")));
        $Emailfieldset->addField("emailcontent", "textarea", array(
            "label" => Mage::helper("latepayment")->__("Email content"),
            "class" => "required-entry",
            "required" => false,
            "name" => "emailcontent",
        ));


        if (Mage::getSingleton("adminhtml/session")->getLatepaymentData()) {
            $form->setValues(Mage::getSingleton("adminhtml/session")->getLatepaymentData());
            Mage::getSingleton("adminhtml/session")->setLatepaymentData(null);
        } elseif (Mage::registry("latepayment_data")) {
            $form->setValues(Mage::registry("latepayment_data")->getData());
        }
        return parent::_prepareForm();
    }

}
