<?php


class Ant_Deliverymanagement_Block_Adminhtml_Arrangedriver extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_arrangedriver";
	$this->_blockGroup = "deliverymanagement";
	$this->_headerText = Mage::helper("deliverymanagement")->__("Arrange Driver");
	//$this->_addButtonLabel = Mage::helper("deliverymanagement")->__("Add New Item");
	parent::__construct();
        $this->removeButton('add');
	}

}