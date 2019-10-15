<?php
class MW_FreeGift_Block_Quote extends Mage_Core_Block_Template
{	
	public function _construct(){
		$this->setTemplate('mw_freegift/quote.phtml');
	}
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    public function getAllActiveRules()
    {
    	$quote = Mage::getSingleton('checkout/session')->getQuote();
    	$websiteId = Mage::app()->getStore($quote->getStoreId())->getWebsiteId();
    	$customerGroupId = $quote->getCustomerGroupId()?$quote->getCustomerGroupId():0;
    	$collection = Mage::getModel('freegift/salesrule')->getCollection()->setValidationFilter($websiteId,$customerGroupId)->addFieldToFilter('coupon_code','');
    	$collection->getSelect()->where('((discount_qty > times_used) or (discount_qty=0))');
    	if(!Mage::getStoreConfig('freegift/config/show_reached_rules'))
    	{
    		$aplliedRuleIds = Mage::getSingleton('checkout/session')->getQuote()->getFreegiftAppliedRuleIds();
    		if(sizeof($aplliedRuleIds))
    			$collection->addFieldToFilter('rule_id',array('nin'=>explode(',',$aplliedRuleIds)));
    	}
    	return $collection;
    }
    public function getRandomRule()
    {
    	$ids = $this->getAllActiveRules()->getAllIds();
    	$rand_key = array_rand($ids);
    	return Mage::getModel('freegift/salesrule')->load($ids[$rand_key]);
    }
    
	public function _toHtml()
    {
    	if(!Mage::getStoreConfig('freegift/config/enabled')) return '';
    	if(!sizeof($this->getAllActiveRules())) return '';
    	$html = $this->renderView();
        return $html;
    }
}