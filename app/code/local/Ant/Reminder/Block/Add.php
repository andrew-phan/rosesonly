<?php   
class Ant_Reminder_Block_Add extends Mage_Core_Block_Template{   
   	public function __construct() {  
		parent::__construct();  			
		$this->setFormAction(Mage::getUrl('reminder/add'));  
	}
}