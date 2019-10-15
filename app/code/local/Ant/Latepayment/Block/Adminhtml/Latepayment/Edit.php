<?php

class Ant_Latepayment_Block_Adminhtml_Latepayment_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {

        parent::__construct();
        $this->_objectId = "entity_id";
        $this->_blockGroup = "latepayment";
        $this->_controller = "adminhtml_latepayment";
        $this->_updateButton("save", "label", Mage::helper("latepayment")->__("Save Item"));
        $this->_updateButton("delete", "label", Mage::helper("latepayment")->__("Delete Item"));

        $this->_addButton("reminder", array(
            "label" => Mage::helper("latepayment")->__("Reminder"),
            "onclick" => "saveAndContinueEdit()",
            "class" => "save",
                ), 0);
        $this->_addButton("view", array(
            "label" => Mage::helper("latepayment")->__("View Order"),
            "onclick" => "",
            ), 2);
        $this->_addButton("cancel", array(
            "label" => Mage::helper("latepayment")->__("Cancel Order"),
            "onclick" => "",
            "class" => "delete",
            ), 4);
        
        $this->_removeButton('delete');
        $this->_removeButton('save');

        $this->_formScripts[] = "function saveAndContinueEdit(){
                                    editForm.submit($('edit_form').action+'back/edit/');}";
    }

    public function getHeaderText() {
        if (Mage::registry("latepayment_data") && Mage::registry("latepayment_data")->getId()) {

            return Mage::helper("latepayment")->__("Order #%s", $this->htmlEscape(Mage::registry("latepayment_data")->getIncrement_id()));
        } else {

            return Mage::helper("latepayment")->__("Add Item");
        }
    }

}