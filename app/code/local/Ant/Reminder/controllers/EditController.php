<?php
class Ant_Reminder_EditController extends Mage_Core_Controller_Front_Action{

    public function indexAction() {
		
		$this->loadLayout();  
		$this->getLayout()->getBlock("head")->setTitle($this->__("Edit Reminder"));
		$breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
		$breadcrumbs->addCrumb("home", array(
			"label" => $this->__("Home Page"),
			"title" => $this->__("Home Page"),
			"link"  => Mage::getBaseUrl()
		));

		$breadcrumbs->addCrumb("reminder", array(
			"label" => $this->__("Reminder"),
			"title" => $this->__("Reminder")
		));

		$this->renderLayout(); 
				
    }
}