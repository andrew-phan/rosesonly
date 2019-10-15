<?php

class Ant_Latepayment_Block_Adminhtml_Latepayment extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        $this->_controller = "adminhtml_latepayment";
        $this->_blockGroup = "latepayment";
        $this->_headerText = Mage::helper("latepayment")->__("Latepayment");
        //$this->_addButtonLabel = Mage::helper("latepayment")->__("Add New Item");
        parent::__construct();
        $this->_removeButton('add');
        $this->addButton('btnRefesh', array('label' => $this->__('Refesh'),
            'onclick' => 'setLocation(\'' . $this->getRefeshUrl() . '\')',
            'class' => 'go'
        ));
    }

    public function getRefeshUrl() {
        return Mage::getModel('adminhtml/url')->getUrl('latepayment/adminhtml_latepayment/refesh');
    }

}