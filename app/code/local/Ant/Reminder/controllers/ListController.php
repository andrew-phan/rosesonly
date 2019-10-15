<?php
class Ant_Reminder_ListController extends Mage_Core_Controller_Front_Action{

	
    public function indexAction() {
			
        $this->getResponse()->setHeader('Login-Required', 'true');
        
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        
		
	$this->loadLayout();   
	  $this->getLayout()->getBlock("head")->setTitle($this->__("Customer Reminder"));
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
		   
		$customer = Mage::getSingleton('customer/session')->getCustomer();
		$list = Mage::getModel('reminder/reminder')->getCollection()->addFieldToFilter('customer_id',$customer->getId());

		$this->getLayout()->getBlock('reminder_list')->assign('data',$list );
		
		
			
      $this->renderLayout(); 
				
    }
	
	public function deleteAction(){
		$id =  $this->getRequest()->getParam('id');
		$del = Mage::getModel('reminder/reminder');
		$del->setId($id);
		$del->delete();
		$this->_redirect('reminder/manage', array('_secure'=>true));	
	}
}