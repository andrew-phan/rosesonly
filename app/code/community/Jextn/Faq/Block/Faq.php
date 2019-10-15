<?php
class Jextn_Faq_Block_Faq extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		$this->getLayout()->getBlock('head')->setTitle(Mage::helper('faq')->getFAQTitle());
		return parent::_prepareLayout();
    }
    
	public function getFaqCollection()
	{
		$storeId = Mage::app()->getStore()->getId();   
		$collection = Mage::getModel('faq/faq')->getCollection()
						->addStoreFilter($storeId)
						->addIsActiveFilter();
		return $collection;
	}
}