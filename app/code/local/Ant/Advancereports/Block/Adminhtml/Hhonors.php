<?php

class Ant_Advancereports_Block_Adminhtml_Hhonors extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() { 
        $this->_controller = 'adminhtml_hhonors';
        $this->_blockGroup = 'advancereports';
        $this->_headerText = Mage::helper('advancereports')->__('HHonors Order Reports');
        parent::__construct();

        $this->_removeButton('add');
    }

}