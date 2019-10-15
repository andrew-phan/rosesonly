<?php
class MW_FreeGift_Model_Observer extends Mage_Core_Model_Abstract
{
	protected $_validator;
	protected function _getMaxFreeItem()
    {
    	//return sizeof(Mage::getModel('freegift/salesrule')->getCollection()->addFieldToFilter('is_active',1));
//    	if(sizeof(Mage::getModel('freegift/salesrule')->getCollection()->addFieldToFilter('is_active',1)->addFieldToFilter('stop_rules_processing',1)) == 0)
//    		return sizeof(Mage::getModel('freegift/salesrule')->getCollection()->addFieldToFilter('is_active',1)->addFieldToFilter('stop_rules_processing',0));
//    	else 
//    		return 1;	
//		$freeids = Mage::getSingleton('checkout/session')->getQuote()->getFreegiftIds();
//    	$kbc = explode(",", $freeids);
//    	foreach ($kbc as $value) {
//    		$abc = $this->getRuleByFreeProductId($value);
//    		if($abc)
//    			$arr[] = $abc->getId();
//    	}
//    	$dem = 1;
//    	ksort($arr);
//		for($i=1;$i<sizeof($arr);$i++){
//			if($arr[$i] != $arr[$i-1])$dem++;
//		}
//		//echo 'dem: ' . $dem;die;
		return Mage::getSingleton('core/session')->getCountFreeGift();
    }
    
    protected function _getNumberOfAddedFreeItems(){
    	$items = Mage::getSingleton('checkout/session')->getQuote()->getAllVisibleItems();
    	$countFreeItem = 0;
    	foreach($items as $item){
    		if($item->getParentItem()) continue;
    		$params = unserialize($item->getOptionByCode('info_buyRequest')->getValue());
        	if(isset($params['freegift']) && $params['freegift']) {
        		$countFreeItem ++;
        	}
    	}
    	return $countFreeItem;
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
    
    
    protected function getAplliedRule($request)
    {
    	if(isset($request['apllied_rule']) && $request['apllied_rule'])
    		return Mage::getModel('freegift/salesrule')->load($request['apllied_rule']);
    	return false;
    }
    
    protected function _getSession()
    {
    	return Mage::getSingleton('checkout/session');
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
    
    public function checkoutCartProductAddAfter($argv){
    	if(!Mage::getStoreConfig('freegift/config/enabled')) return false;
    	$_product = $argv->getProduct();    	
    	$quote_item = $argv->getQuoteItem();
    	$infoRequest = unserialize($quote_item->getOptionByCode('info_buyRequest')->getValue());
    	
    	/* Free items with coupon code */
    	if(isset($infoRequest['freegift_with_code']) && $infoRequest['freegift_with_code']){
    			$quote_item->setQty(1);
        		$quote_item->setPrice(0);
        		$quote_item->setOriginalPrice(0);
        		$quote_item->setBaseOriginalPrice(0);
        		$quote_item->setCalculationPrice(0);
        		$quote_item->setBaseCalculationPrice(0);
        		$quote_item->setOriginalCustomPrice(0);
        		$quote_item->setCustomPrice(0);
        		$quote_item->setRowTotal(0);
        		$quote_item->setBaseRowTotal(0);
        		$quote_item->setRowTotalWithDiscount(0);
        		$quote_item->setBaseRowTotalWithDiscount(0);
        		$quote_item->setTaxAmount(0);
        		$quote_item->setBaseTaxAmount(0);
        		$quote_item->setConvertedPrice(0);
        		$infoRequest['option']= serialize(array('label'=>Mage::helper('freegift')->__('Free Gift with coupon Code'), 'value'=>$infoRequest['freegift_coupon_code']));
        		$quote_item->getOptionByCode('info_buyRequest')->setValue(serialize($infoRequest));
        		
        		$_options = array(
	                    1=>array(
	                        'label' => Mage::helper('freegift')->__('Free Gift with coupon Code'),
	                        'value' => $infoRequest['freegift_coupon_code'],
	                        'print_value' => $infoRequest['freegift_coupon_code'],
	                        'option_type' => 'text',
	                        'custom_view' => true
	                    )
	            );
	            $options = array(
					'code'=>'additional_options',
					'value'=>serialize($_options)
				);
				$quote_item->addOption($options);
    	}
    	/* Shopping cart Free Gift */
    	//echo 'number of add: ' . $this->_getNumberOfAddedFreeItems();
    	if(isset($infoRequest['freegift']) && $infoRequest['freegift'] && ($this->_getNumberOfAddedFreeItems() <= $this->_getMaxFreeItem())){
	    	$rule = $this->getAplliedRule($infoRequest);
	    	//Zend_debug::dump(explode(',', $rule->getData('gift_product_ids')));
	    	if($rule && in_array($_product->getId(),explode(',', $rule->getData('gift_product_ids')))){
		    	if(($rule->getData('discount_qty') > $rule->getData('times_used'))|| !$rule->getData('discount_qty')){
		    		$quote_item->setPrice(0);
	        		$quote_item->setOriginalPrice(0);
	        		$quote_item->setBaseOriginalPrice(0);
	        		$quote_item->setCalculationPrice(0);
	        		$quote_item->setBaseCalculationPrice(0);
	        		$quote_item->setOriginalCustomPrice(0);
	        		$quote_item->setCustomPrice(0);
	        		$quote_item->setRowTotal(0);
	        		$quote_item->setBaseRowTotal(0);
	        		$quote_item->setRowTotalWithDiscount(0);
	        		$quote_item->setBaseRowTotalWithDiscount(0);
	        		$quote_item->setTaxAmount(0);
	        		$quote_item->setBaseTaxAmount(0);
	        		$quote_item->setConvertedPrice(0);
	        		$infoRequest['option']= serialize(array('label'=>Mage::helper('freegift')->__('Free Gift'), 'value'=>$rule->getDescription()));
        			$quote_item->getOptionByCode('info_buyRequest')->setValue(serialize($infoRequest));
        			
	        		$_options = array(
	                    1=>array(
	                        'label' => Mage::helper('freegift')->__('Free Gift'),
	                        'value' => $rule->getDescription(),
	                        'print_value' => $rule->getDescription(),
	                        'option_type' => 'text',
	                        'custom_view' => true
	                    )
		            );
		            $options = array(
						'code'=>'additional_options',
						'value'=>serialize($_options)
					);
					$quote_item->addOption($options);
					
		    		$this->_getSession()->getQuote()->setTotalsCollectedFlag(false);
					$this->_getSession()->getQuote()->collectTotals();
					$this->_getSession()->getQuote()->setTotalsCollectedFlag(false);
		    	}
	    	}else{
	    		unset($infoRequest['freegift']);
	    		$quote_item->getOptionByCode('info_buyRequest')->setValue(serialize($infoRequest));
	    	}
    	}else{
    		//$quote_item->addOption(array('code'=>'free_gift','value'=>0));
    	}
    	
    	/* Catalog Free Gift */
    	if(isset($infoRequest['free_catalog_gift']) && $infoRequest['free_catalog_gift'])
    	{
    		/* Add custom option to catalog gift*/
    		$parentGiftItem = $this->_getFreeGiftItemByGiftKey($infoRequest['freegift_parent_key'],$quote_item->getQuote());
    		$_infoRequest = unserialize($parentGiftItem->getOptionByCode('info_buyRequest')->getValue());
    		foreach(unserialize($_infoRequest['apllied_rules']) as $ruleId)
    		{
    			$tmpRule = Mage::getModel('freegift/rule')->load($ruleId);
    			if(in_array($_product->getId(), explode(',', $tmpRule->getData('gift_product_ids'))))
    			{
    				$infoRequest['option']= serialize(array('label'=>Mage::helper('freegift')->__('Free Gift'), 'value'=>$tmpRule->getDescription()));
        			$quote_item->getOptionByCode('info_buyRequest')->setValue(serialize($infoRequest));
    				$_options = array(
	                    1=>array(
	                        'label' => Mage::helper('freegift')->__('Free Gift'),
	                        'value' => $tmpRule->getDescription(),
	                        'print_value' => $tmpRule->getDescription(),
	                        'option_type' => 'text',
	                        'custom_view' => true
	                    )
		            );
		            $options = array(
						'code'=>'additional_options',
						'value'=>serialize($_options)
					);
					$quote_item->addOption($options);
					break;
    			}
    			
    		}
    	}
    	
    	if($_product->getTypeId() == 'grouped') return;
    	$freegiftProduct = Mage::getModel('freegift/product')->init($_product);
    	$freeproducts = $freegiftProduct->getFreeGifts();
    	$cart = Mage::getSingleton('checkout/cart');
    	if($freeproducts && sizeof($freeproducts)){
    		//Catalog Rules
    		$applied_rule_ids = $freegiftProduct->getAplliedRuleIds();
    		$this->_getSession()->getQuote()->collectTotals();
    		$randKey = md5(rand(1111,9999));
	    	if((isset($infoRequest['free_catalog_gift']) && $infoRequest['free_catalog_gift']) || (isset($infoRequest['freegift']) && $infoRequest['freegift']) || (isset($infoRequest['freegift_with_code']) && $infoRequest['freegift_with_code'])) return;
	    	if(!isset($infoRequest['freegift_key'])){
	    		$infoRequest['freegift_key']=$randKey;
	    		$infoRequest['apllied_rules'] = serialize($applied_rule_ids);
	    	}
	    	$quote_item->getOptionByCode('info_buyRequest')->setValue(serialize($infoRequest));
	    	
	    	foreach($freeproducts as $productId){
	    		$product = $this->_initProduct($productId);
	    		$product->addCustomOption('free_catalog_gift',1);
	    		$product->addCustomOption('freegift_parent_key',$randKey);
	    		$product->setPrice(0);
	    		$request = array('uenc'=>$infoRequest['uenc'],'product'=>$product->getId(),'qty'=>$quote_item->getQty(),'free_catalog_gift'=>1,'freegift_parent_key'=>$randKey);
	    		
	    		$cart->addProduct($product,$request);
	    		$cart->save();
	    		Mage::getSingleton('checkout/session')->setCartWasUpdated(false);

    			$this->_getSession()->addSuccess(Mage::helper('freegift')->__('%s was automaticly added to your shopping cart',$product->getName()));
	    	}
	    	$this->_getSession()->getQuote()->setTotalsCollectedFlag(false);
			$this->_getSession()->getQuote()->collectTotals();
			$this->_getSession()->getQuote()->setTotalsCollectedFlag(false);
    	}
    }
	
    public function checkout_cart_add_product_complete($argvs)
    {
    	
    }
    
    public function catalog_product_type_prepare_full_options($argvs)
    {
    	$transport 	= $argvs->getTransport();
    	$buyRequest = $argvs->getBuyRequest();
    	$product 	= $argvs->getProduct();
    	if($buyRequest->getFreegift())
    		$transport->options['freegift'] = '1';
    }
    public function processFrontFinalPrice($observer)
    {
    	$product    = $observer->getEvent()->getProduct();
        $pId        = $product->getId();
        $storeId    = $product->getStoreId();
        if($product->getCustomOption('free_catalog_gift'))
        {
        	$product->setFinalPrice(0);
        }
    }
	
	public function sales_order_afterPlace($observer)
    {
    	$order = $observer->getEvent()->getOrder();
    	if (!$order) {
            return $this;
        }
        
        foreach($order->getAllItems() as $item){
        	if($item->getParentItem()) continue;        	
        	//Catalog rules
        	$infoRequest = $item->getProductOptionByCode('info_buyRequest');
        	if(isset($infoRequest['apllied_rules']) && $infoRequest['apllied_rules']){
        		$apllied_rules = unserialize($infoRequest['apllied_rules']);
        		if(sizeof($apllied_rules)) foreach($apllied_rules as $rule_id)
        		{
        			$rule = Mage::getModel('freegift/rule')->load($rule_id);
        			//$rule->setTimesUsed($rule->getTimesUsed() + 1)->save();
					// +1 for method 
					$update_data['rule_id'] = $rule->getId();
					$update_data['times_used'] = intval($rule->getTimesUsed()) + 1 ; 
					$rule->setData($update_data);
					$rule->save();
        			
        			$freegiftProducts = Mage::getModel('freegift/product')->getCollection()->addFieldToFilter('rule_id',$rule_id);
        			foreach ($freegiftProducts as $product)
        			{
        				$product->setTimesUsed($product->getTimesUsed() + 1)->save();
        			}
        		}
        	}
        	//Sales Rules
        	if(isset($infoRequest['apllied_rule']) && $infoRequest['apllied_rule']){
        		$rule = Mage::getModel('freegift/salesrule')->load($infoRequest['apllied_rule']);
        		//$rule->setTimesUsed($rule->getTimesUsed() + 1)->save();
				$update_data['rule_id'] = $rule->getId();
				$update_data['times_used'] = intval($rule->getTimesUsed()) + 1 ; 
				$rule->setData($update_data);
				$rule->save();
        	}
        	
        	if(isset($infoRequest['freegift_with_code']) && $infoRequest['freegift_with_code']){
        		$rule = Mage::getModel('freegift/salesrule')->load($infoRequest['rule_id']);
        		//$rule->setTimesUsed($rule->getTimesUsed() + 1)->save();
				$update_data['rule_id'] = $rule->getId();
				$update_data['times_used'] = intval($rule->getTimesUsed()) + 1 ; 
				$rule->setData($update_data);
				$rule->save();
        	}
        }
    }
	/**
     * Get quote item validator/processor object
     *
     * @deprecated
     * @param   Varien_Event $event
     * @return  MW_FreeGift_Model_Validator
     */
    public function getValidator($event)
    {
        if (!$this->_validator) {
            $this->_validator = Mage::getModel('freegift/validator')
                ->init($event->getWebsiteId(), $event->getCustomerGroupId(), $event->getFreegiftCouponCode());
        }
        return $this->_validator;
    }
    public function freegift_quote_address_freegift_item($observer)
    {
    	$this->getValidator($observer->getEvent())
            ->process($observer->getEvent()->getItem());
    }
    
	/**
     * Append sales rule product attributes to select by quote item collection
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_SalesRule_Model_Observer
     */
    public function addProductAttributes(Varien_Event_Observer $observer)
    {
        // @var Varien_Object
        $attributesTransfer = $observer->getEvent()->getAttributes();

        $attributes = Mage::getResourceModel('freegift/salesrule')
            ->getActiveAttributes(
                Mage::app()->getWebsite()->getId(),
                Mage::getSingleton('customer/session')->getCustomer()->getGroupId()
            );
        $result = array();
        foreach ($attributes as $attribute) {
            $result[$attribute['attribute_code']] = true;
        }
        $attributesTransfer->addData($result);
        return $this;
    }
    /**
     * Fix catalog rule with grouped product 
     * @param unknown_type $observer
     */
    public function sales_quote_product_add_after(Varien_Event_Observer $observer){
    	$items = $observer->getItems();
    	$groups = array();
    	foreach($items as $item){
    		$infoRequest = unserialize($item->getOptionByCode('info_buyRequest')->getValue());
    		$superConfig = $infoRequest['super_product_config'];
    		if(isset($superConfig) && is_array($superConfig)){
	    		if($superConfig['product_type']=='grouped'){
	    			$_product = Mage::getModel('catalog/product')->load($superConfig['product_id']);
	    			$freegiftProduct = Mage::getModel('freegift/product')->init($_product);
			    	$freeproducts = $freegiftProduct->getFreeGifts();
			    	$cart = Mage::getSingleton('checkout/cart');
			    	if($freeproducts && sizeof($freeproducts)){
			    		//Catalog Rules
			    		$applied_rule_ids = $freegiftProduct->getAplliedRuleIds();
			    		$this->_getSession()->getQuote()->collectTotals();
			    		$randKey = md5(rand(1111,9999));
				    	if((isset($infoRequest['free_catalog_gift']) && $infoRequest['free_catalog_gift']) || (isset($infoRequest['freegift']) && $infoRequest['freegift']) || (isset($infoRequest['freegift_with_code']) && $infoRequest['freegift_with_code'])) return;
				    	if(!isset($infoRequest['freegift_key'])){
				    		$infoRequest['freegift_key']=$randKey;
				    		$infoRequest['apllied_rules'] = serialize($applied_rule_ids);
				    	}
				    	$item->getOptionByCode('info_buyRequest')->setValue(serialize($infoRequest));
				    	
				    	foreach($freeproducts as $productId){
				    		$product = $this->_initProduct($productId);
				    		$product->addCustomOption('free_catalog_gift',1);
				    		$product->addCustomOption('freegift_parent_key',$randKey);
				    		$product->setPrice(0);
				    		$request = array('uenc'=>$infoRequest['uenc'],'product'=>$product->getId(),'qty'=>$item->getQty(),'free_catalog_gift'=>1,'freegift_parent_key'=>$randKey);
				    		
				    		$cart->addProduct($product,$request);
				    		$cart->save();
				    		Mage::getSingleton('checkout/session')->setCartWasUpdated(false);
			
			    			$this->_getSession()->addSuccess(Mage::helper('freegift')->__('%s was automaticly added to your shopping cart',$product->getName()));
				    	}
	    			}
    			}
    		}
    	}
	    $this->_getSession()->getQuote()->setTotalsCollectedFlag(false);
		$this->_getSession()->getQuote()->collectTotals();
		$this->_getSession()->getQuote()->setTotalsCollectedFlag(false);
    }
}
