<?php
class MW_FreeGift_Model_Quote_Address_Total_Freegift extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
	protected function _getMaxFreeItem()
    {
    	return sizeof(Mage::getModel('freegift/salesrule')->getCollection());
    }
	/**
     * Initialize product instance from request data
     *
     * @return Mage_Catalog_Model_Product || false
     */
    protected function _initProduct($productId)
    {
        if ($productId) {
            $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($productId);
            if ($product->getId()) {
                return $product;
            }
        }
        return false;
    }
    
	protected function _getFreeGiftItemByGiftKey($key,$quote)
    {
    	foreach($quote->getAllItems() as $item)
    	{
    		$params = unserialize($item->getOptionByCode('info_buyRequest')->getValue());
    		if(isset($params['freegift_key']) && ($params['freegift_key'] == $key)) return $item;
    	}
    	return false;
    }
	/**
     * Check if rule can be applied for specific address/quote/customer
     *
     * @param   Mage_SalesRule_Model_Rule $rule
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  bool
     */
    protected function _canProcessRule($rule, $address)
    {
    	if(!$rule->getData('is_active')){
        	 return false;
        }
        if($rule->getData('discount_qty') && ($rule->getData('discount_qty') <= $rule->getData('times_used'))){
        	return false;
        }
    	if (!$rule->hasIsValid()) {
            $rule->afterLoad();
            /**
             * quote does not meet rule's conditions
             */
            if (!$rule->validate($address)) {
                $rule->setIsValid(false);
                return false;
            }
            /**
             * passed all validations, remember to be valid
             */
            $rule->setIsValid(true);
        }
        return $rule->getIsValid();

    }
	public function collect(Mage_Sales_Model_Quote_Address $address)
    {
    	if(!Mage::getStoreConfig('freegift/config/enabled')) return false;
    	$quote = $address->getQuote();
        $eventArgs = array(
            'website_id'=>Mage::app()->getStore($quote->getStoreId())->getWebsiteId(),
            'customer_group_id'=>$quote->getCustomerGroupId(),
            'freegift_coupon_code'=>$quote->getFreegiftCouponCode(),
        );
    	
        $items = $address->getAllVisibleItems();
		if (!count($items)) {
            return $this;
        }
        $quote->setFreegiftAppliedRuleIds('');
        $quote->setFreegiftIds('');
        $totalQty = $address->getData('total_qty');
	    foreach($items as $item){
        	$params = unserialize($item->getOptionByCode('info_buyRequest')->getValue());
        	if((isset($params['freegift']) && $params['freegift']) ||(isset($params['free_catalog_gift']) && $params['free_catalog_gift']) || (isset($params['freegift_with_code']) && $params['freegift_with_code'])) {
        		$totalQty --;
        	}
	    }
	    $address->setData('total_qty',$totalQty)->save();
        foreach($items as $item){
        	if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                    foreach ($item->getChildren() as $child) {
                        $eventArgs['item'] = $child;
                        Mage::dispatchEvent('freegift_quote_address_freegift_item', $eventArgs);
                    }
        	}else {
                    $eventArgs['item'] = $item;
                    Mage::dispatchEvent('freegift_quote_address_freegift_item', $eventArgs);
        	}
        }
        
        $countFreeItem = 0;
        $messages = '';
        $freeProductIds = explode(",",$quote->getFreegiftIds());
        /*Reset free gift coupon code */
        $quote->setFreegiftCouponCode('');
        $appliedCoupon = array();
        foreach($items as $item){
        	$params = unserialize($item->getOptionByCode('info_buyRequest')->getValue());
        	if(isset($params['freegift']) && $params['freegift']) {
        		if(!$item->getParentItem())
        			$countFreeItem ++;
        		if($countFreeItem > $this->_getMaxFreeItem())  {
        			$message = Mage::helper('freegift')->__("%s was removed automaticaly",$item->getName());
        			$messages[] = $message;
        			$quote->removeItem($item->getId());
        		}
	        	if(in_array($item->getProductId(),$freeProductIds)){
	        		if($item->getQty() > 1){
	        			$item->setQty(1);
	        			//Mage::getSingleton('checkout/session')->addError(Mage::helper('freegift')->__('You are not allowed update qty of free item'));
	        		}
	        		$item->setPrice(0);
	        		$item->setOriginalPrice(0);
	        		$item->setBaseOriginalPrice(0);
	        		$item->setCalculationPrice(0);
	        		$item->setBaseCalculationPrice(0);
	        		$item->setOriginalCustomPrice(0);
	        		$item->setCustomPrice(0);
	        		$item->setRowTotal(0);
	        		$item->setBaseRowTotal(0);
	        		$item->setRowTotalWithDiscount(0);
	        		$item->setBaseRowTotalWithDiscount(0);
	        		$item->setTaxAmount(0);
	        		$item->setBaseTaxAmount(0);
	        		$item->setConvertedPrice(0);
	        	}else
	        	{
	        		//remove this free items automaticaly
	        		//$message = Mage::helper('freegift')->__("%s was removed automaticaly",$item->getName());
	        		//$messages[] = $message;
	        		if($parentItem = $item->getParentItem())
	        		{
	        			
	        		}else{
	        			$quote->removeItem($item->getId());
	        		}
	        	}
        	
        	}
        	//
        	if(isset($params['free_catalog_gift']) && $params['free_catalog_gift']){
        		$quoteItem = $this->_getFreeGiftItemByGiftKey($params['freegift_parent_key'],$quote);
        		if($quoteItem){
        			
        			if($item->getQty() != $quoteItem->getQty()){
	        			$item->setQty($quoteItem->getQty());
	        			//Mage::getSingleton('checkout/session')->addError(Mage::helper('freegift')->__('The qty of free item must equals or less than %s',$quoteItem->getQty()));
	        		}
	        		$item->setPrice(0);
	        		$item->setOriginalPrice(0);
	        		$item->setBaseOriginalPrice(0);
	        		$item->setCalculationPrice(0);
	        		$item->setBaseCalculationPrice(0);
	        		$item->setOriginalCustomPrice(0);
	        		$item->setCustomPrice(0);
	        		$item->setRowTotal(0);
	        		$item->setBaseRowTotal(0);
	        		$item->setRowTotalWithDiscount(0);
	        		$item->setBaseRowTotalWithDiscount(0);
	        		$item->setTaxAmount(0);
	        		$item->setBaseTaxAmount(0);
	        		$item->setConvertedPrice(0);
        		}else{
        			//$message = Mage::helper('freegift')->__("%s was removed automaticaly",$item->getName());
        			//$messages[] = $message;
        			$quote->removeItem($item->getId());
        		}
        	}
        	
        	if(isset($params['freegift_with_code']) && $params['freegift_with_code']){
        		if(!in_array($params['freegift_coupon_code'], $appliedCoupon))
        			$appliedCoupon[] = $params['freegift_coupon_code'];
        		$rule = Mage::getModel('freegift/salesrule')->load($params['rule_id']);
				if(!$this->_canProcessRule($rule,$address)){
					$quote->removeItem($item->getId());
					continue;
				}
        		$item->setQty(1);
        		$item->setPrice(0);
        		$item->setOriginalPrice(0);
        		$item->setBaseOriginalPrice(0);
        		$item->setCalculationPrice(0);
        		$item->setBaseCalculationPrice(0);
        		$item->setOriginalCustomPrice(0);
        		$item->setCustomPrice(0);
        		$item->setRowTotal(0);
        		$item->setBaseRowTotal(0);
        		$item->setRowTotalWithDiscount(0);
        		$item->setBaseRowTotalWithDiscount(0);
        		$item->setTaxAmount(0);
        		$item->setBaseTaxAmount(0);
        		$item->setConvertedPrice(0);
        	}
        }
        if(sizeof($appliedCoupon)) $quote->setFreegiftCouponCode(serialize($appliedCoupon));
        
        if($messages){foreach($messages as $message)Mage::getSingleton('checkout/session')->addSuccess($message);}
        return parent::collect($address);
    }
    
    
}
