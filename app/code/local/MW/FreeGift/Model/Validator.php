<?php
class MW_FreeGift_Model_Validator extends Mage_Core_Model_Abstract
{
    /**
     * Rule source collection
     *
     * @var Mage_SalesRule_Model_Mysql4_Rule_Collection
     */
    protected $_rules;
    /**
     * Init validator
     * Init process load collection of rules for specific website,
     * customer group and coupon code
     *
     * @param   int $websiteId
     * @param   int $customerGroupId
     * @param   string $freegiftCouponCode
     * @return  Mage_SalesRule_Model_Validator
     */
    public function init($websiteId, $customerGroupId,$freegiftCouponCode)
    {
    	$this->setWebsiteId($websiteId)
            ->setCustomerGroupId($customerGroupId);

        $key = 'freegift_'.$websiteId . '_' . $customerGroupId;
        if (!isset($this->_rules[$key])) {
            $collection = Mage::getResourceModel('freegift/salesrule_collection')
            	->addOrder('sort_order','DESC')
                ->setValidationFilter($websiteId, $customerGroupId)
                ->addFieldToFilter('coupon_code','');
            $collection->getSelect()->where('((discount_qty > times_used) or (discount_qty = 0))');
            $collection->load();
            $this->_rules[$key] = $collection;
        }
        $this->_freegift_ids = array();
        return $this;
    }

    /**
     * Get rules collection for current object state
     *
     * @return Mage_SalesRule_Model_Mysql4_Rule_Collection
     */
    protected function _getRules()
    {
        $key = 'freegift_'.$this->getWebsiteId() . '_' . $this->getCustomerGroupId();
        return $this->_rules[$key];
    }

    /**
     * Get address object which can be used for discount calculation
     *
     * @param   Mage_Sales_Model_Quote_Item_Abstract $item
     * @return  Mage_Sales_Model_Quote_Address
     */
    protected function _getAddress(Mage_Sales_Model_Quote_Item_Abstract $item)
    {
        if ($item instanceof Mage_Sales_Model_Quote_Address_Item) {
            $address = $item->getAddress();
        } elseif ($item->getQuote()->isVirtual()) {
            $address = $item->getQuote()->getBillingAddress();
        } else {
            $address = $item->getQuote()->getShippingAddress();
        }
        return $address;
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
     * Quote item discount calculation process
     *
     * @param   Mage_Sales_Model_Quote_Item_Abstract $item
     * @return  Mage_SalesRule_Model_Validator
     */
    public function process(Mage_Sales_Model_Quote_Item_Abstract $item)
    {
        $quote      = $item->getQuote();
        $address    = $this->_getAddress($item);

        $itemPrice  = $this->_getItemPrice($item);
        $baseItemPrice = $this->_getItemBasePrice($item);

        if ($itemPrice <= 0) {
            return $this;
        }
		$freegiftIds = array();
		$appliedRuleIds = array();
        foreach ($this->_getRules() as $rule) {
            /* @var $rule Mage_SalesRule_Model_Rule */
            if (!$this->_canProcessRule($rule, $address)) {
                continue;
            }

            $appliedRuleIds[$rule->getRuleId()] = $rule->getRuleId();
			$freegiftIds = $this->mergeIds($freegiftIds,$rule->getData('gift_product_ids'));
            if ($rule->getStopRulesProcessing()) {
                break;
            }
        }
        $quote->setFreegiftAppliedRuleIds($this->mergeIds($quote->getFreegiftAppliedRuleIds(), $appliedRuleIds));
        $quote->setFreegiftIds($this->mergeIds($quote->getFreegiftIds(), $freegiftIds));
        $this->_max_free_item = 1;
        return $this;
    }
    /**
     * Merge two sets of ids
     *
     * @param array|string $a1
     * @param array|string $a2
     * @param bool $asString
     * @return array
     */
    public function mergeIds($a1, $a2, $asString = true)
    {
        if (!is_array($a1)) {
            $a1 = empty($a1) ? array() : explode(',', $a1);
        }
        if (!is_array($a2)) {
            $a2 = empty($a2) ? array() : explode(',', $a2);
        }
        $a = array_unique(array_merge($a1, $a2));
        if ($asString) {
           $a = implode(',', $a);
        }
        return $a;
    }


    /**
     * Return item price
     *
     * @param Mage_Sales_Model_Quote_Item_Abstract $item
     * @return float
     */
    protected function _getItemPrice($item)
    {
        $price = $item->getDiscountCalculationPrice();
        return ($price !== null) ? $price : $item->getCalculationPrice();
    }

    /**
     * Return item base price
     *
     * @param Mage_Sales_Model_Quote_Item_Abstract $item
     * @return float
     */
    protected function _getItemBasePrice($item)
    {
        $price = $item->getDiscountCalculationPrice();
        return ($price !== null) ? $item->getBaseDiscountCalculationPrice() : $item->getBaseCalculationPrice();
    }

    /**
     * Return discount item qty
     *
     * @param Mage_Sales_Model_Quote_Item_Abstract $item
     * @param Mage_SalesRule_Model_Rule $rule
     * @return int
     */
    protected function _getItemQty($item, $rule)
    {
        $qty = $item->getTotalQty();
        return $rule->getDiscountQty() ? min($qty, $rule->getDiscountQty()) : $qty;
    }
}
