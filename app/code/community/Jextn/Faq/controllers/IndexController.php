<?php
class Jextn_Faq_IndexController extends Mage_Core_Controller_Front_Action
{
	const XML_PATH_ENABLED   = 'faq/faq/enabled';
    public function preDispatch()
    {
        parent::preDispatch();

        if( !Mage::getStoreConfigFlag(self::XML_PATH_ENABLED) ) {
            $this->norouteAction();
        }
    }
    public function indexAction()
    {			
		$this->loadLayout();     
		$this->renderLayout();
    }
}