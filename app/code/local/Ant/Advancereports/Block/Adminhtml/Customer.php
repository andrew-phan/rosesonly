<?php

class Ant_Advancereports_Block_Adminhtml_Customer extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() { 
        $this->_controller = 'adminhtml_customer';
        $this->_blockGroup = 'advancereports';
        $this->_headerText = Mage::helper('advancereports')->__('Customer Reports');
        parent::__construct();

        $this->_removeButton('add');
    }

}