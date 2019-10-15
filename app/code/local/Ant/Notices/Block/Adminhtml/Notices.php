<?php


class Ant_Notices_Block_Adminhtml_Notices extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_notices";
	$this->_blockGroup = "notices";
	$this->_headerText = Mage::helper("notices")->__("Notices Manager");
	$this->_addButtonLabel = Mage::helper("notices")->__("Add Notice");
	parent::__construct();

	}

}