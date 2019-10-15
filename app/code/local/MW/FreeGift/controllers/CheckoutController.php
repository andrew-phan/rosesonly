<?php
class MW_FreeGift_CheckoutController extends Mage_Core_Controller_Front_Action
{
	
	protected function _getSession()
	{
		return Mage::getSingleton('checkout/session');
	}
	/**
     * Retrieve shopping cart model object
     *
     * @return Mage_Checkout_Model_Cart
     */
    protected function _getCart()
    {
        return Mage::getSingleton('checkout/cart');
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
    
	public function couponPostAction(){
		$couponCode = (string) $this->getRequest()->getParam('freegift_coupon_code');
	    if($couponCode){
	    	$rule = Mage::getModel('freegift/salesrule')->load($couponCode,'coupon_code');
	    	if($rule->getId()){
	    		$now = Mage::getModel('core/date')->date('Y-m-d');
	    		if((!$rule->getFromDate() || $now >= $rule->getFromDate()) && (!$rule->getToDate() || $now <= $rule->getToDate())){
	    			$quote = $this->_getSession()->getQuote();
	    			$address = $quote->isVirtual()?$quote->getBillingAddress():$quote->getShippingAddress();
	    			if($this->_canProcessRule($rule, $address)){
	    				$appliedCode = unserialize($quote->getFreegiftCouponCode());
	    				if(!is_array($appliedCode) || !in_array($couponCode, $appliedCode)){
	    					$appliedCode[] = $couponCode;
	    					$quote->setFreegiftCouponCode(serialize($appliedCode))->setTotalsCollectedFlag(false)->collectTotals()->save();
				            $valid_code = true;
				            try{
				            	/* Automatically add free product*/
				            	$productIds = explode(",",$rule->getData('gift_product_ids'));
				            	$cart = $this->_getCart();
				    			foreach($productIds as $productId){
						    		$product = $this->_initProduct($productId);
						    		$product->addCustomOption('freegift_with_code',1);
						    		$product->setPrice(0);
						    		$request = array('product'=>$product->getId(),'qty'=>1,'freegift_with_code'=>1,'freegift_coupon_code'=>$couponCode,'rule_id'=>$rule->getId());
						    		$cart->addProduct($product,$request);
						    		$cart->save();
						    		Mage::getSingleton('checkout/session')->setCartWasUpdated(false);
					
					    			$this->_getSession()->addSuccess(Mage::helper('freegift')->__('%s was automaticly added to your shopping cart',$product->getName()));
						    	}
				            }catch (Mage_Core_Exception $e){
				            	$valid_code = false;
				            }catch (Exception $e1){ $valid_code = false;}
	    				}else {$valid_code = false;}
	    			}else{
				    	$valid_code = false;
				    }
	    		}else{
			    	$valid_code = false;
			    }
	    	}else{
	    		$valid_code = false;
	    	}
	    }else {$valid_code = false;}
	    if($valid_code)
	    {
	    	$this->_getSession()->addSuccess(
            	$this->__('Free gift code "%s" was applied.', Mage::helper('core')->htmlEscape($couponCode))
            );
	    }else{
	    	$this->_getSession()->addError(
            	$this->__('Free gift code "%s" is not valid.', Mage::helper('core')->htmlEscape($couponCode))
            );
	    }
	    
		$this->_redirect('checkout/cart/index');
	}
}