<?php
class MW_FreeGift_Block_Product extends Mage_Core_Block_Template
{	
	protected $_priceBlock = array();
	
	protected $_block = 'catalog/product_price';

    protected $_priceBlockDefaultTemplate = 'catalog/product/price.phtml';

    protected $_tierPriceDefaultTemplate  = 'catalog/product/view/tierprices.phtml';
    
    protected $_priceBlockTypes = array();
    
    public function _beforeToHtml(){
    	if($this->getVertical())
    		$this->setTemplate('mw_freegift/freegift_vertical.phtml');
    	else
    		$this->setTemplate('mw_freegift/freegift.phtml');
    }
    
 	public function getPriceBlockTemplate()
    {
        return $this->_getData('freegift_price_block_template');
    }
	protected function _getPriceBlock($productTypeId)
    {
        if (!isset($this->_priceBlock[$productTypeId])) {
            $block = $this->_block;
            if (isset($this->_priceBlockTypes[$productTypeId])) {
                if ($this->_priceBlockTypes[$productTypeId]['block'] != '') {
                    $block = $this->_priceBlockTypes[$productTypeId]['block'];
                }
            }
            $this->_priceBlock[$productTypeId] = $this->getLayout()->createBlock($block);
        }
        return $this->_priceBlock[$productTypeId];
    }

    protected function _getPriceBlockTemplate($productTypeId)
    {
        if (isset($this->_priceBlockTypes[$productTypeId])) {
            if ($this->_priceBlockTypes[$productTypeId]['template'] != '') {
                return $this->_priceBlockTypes[$productTypeId]['template'];
            }
        }
        return $this->_priceBlockDefaultTemplate;
    }


    /**
     * Prepares and returns block to render some product type
     *
     * @param string $productType
     * @return Mage_Core_Block_Template
     */
    public function _preparePriceRenderer($productType)
    {
        return $this->_getPriceBlock($productType)
            ->setTemplate($this->_getPriceBlockTemplate($productType))
            ->setUseLinkForAsLowAs($this->_useLinkForAsLowAs);
    }

    /**
     * Returns product price block html
     *
     * @param Mage_Catalog_Model_Product $product
     * @param boolean $displayMinimalPrice
     * @param string $idSuffix
     * @return string
     */
    public function getPriceHtml($product, $displayMinimalPrice = false, $idSuffix = '')
    {
        return $this->_preparePriceRenderer($product->getTypeId())
            ->setProduct($product)
            ->setDisplayMinimalPrice($displayMinimalPrice)
            ->setIdSuffix($idSuffix)
            ->toHtml();
    }
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    public function getRuleByFreeProductId($productId)
    {
		$quote = Mage::getSingleton('checkout/session')->getQuote();
    	$aplliedRules = $quote->getFreegiftAppliedRuleIds();
    	$aplliedRules = explode(',', $aplliedRules);
    	foreach($aplliedRules as $rule_id){
    		$rule = Mage::getModel('freegift/salesrule')->load($rule_id);
    		$productIds = explode(',', $rule->getData('gift_product_ids'));
    		if(in_array($productId, $productIds)){
    			return $rule;
    		}
    	}
    	return false;
    }

	public function getFreeProducts()
    {
    	if($freeids = Mage::getSingleton('checkout/session')->getQuote()->getFreegiftIds())
    	return explode(",", $freeids);
    	return false;
    }
    
    public function getNumberOfAddedFreeItems(){
    	$items = Mage::getSingleton('checkout/session')->getQuote()->getAllVisibleItems();
    	$countFreeItem = 0;
    	foreach($items as $item){
    		$params = unserialize($item->getOptionByCode('info_buyRequest')->getValue());
        	if(isset($params['freegift']) && $params['freegift']) {
        		$countFreeItem ++;
        	}
    	}
    	return $countFreeItem;
    }
    
    public function getMaxFreeItem()
    {
//    	if(sizeof(Mage::getModel('freegift/salesrule')->getCollection()->addFieldToFilter('is_active',1)->addFieldToFilter('stop_rules_processing',1)) == 0)
//    		return sizeof(Mage::getModel('freegift/salesrule')->getCollection()->addFieldToFilter('is_active',1)->addFieldToFilter('stop_rules_processing',0));
//    	else 
//    		return 1;	
    	$kbc = $this->getFreeProducts();
    	foreach ($kbc as $value) {
    		$abc = $this->getRuleByFreeProductId($value);
    		if($abc)
    			$arr[] = $abc->getId();
    	}
    	$dem = 1;
    	ksort($arr);
		for($i=1;$i<sizeof($arr);$i++){
			if($arr[$i] != $arr[$i-1])$dem++;
		}
		Mage::getSingleton('core/session')->setCountFreeGift($dem);
		return $dem;
    }
    
    public function _toHtml()
    {
    	if(!Mage::getStoreConfig('freegift/config/enabled')) return '';
    	//echo 'Number of Free: ' . $this->getNumberOfAddedFreeItems() . ', ' . $this->getMaxFreeItem();
    	if(!sizeof($this->getFreeProducts()) || ($this->getNumberOfAddedFreeItems() >= $this->getMaxFreeItem())) return '';
    	$html = $this->renderView();
        return $html;
    }
    
	/**
     * Retrieve url for add product to cart
     * Will return product view page URL if product has required options
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $additional
     * @return string
     */
    public function getAddToCartUrl($product, $additional = array())
    {
        if ($product->getTypeInstance(true)->hasRequiredOptions($product)) {
            if (!isset($additional['_escape'])) {
                $additional['_escape'] = true;
            }
            if (!isset($additional['_query'])) {
                $additional['_query'] = array();
            }
            $additional['_query']['options'] = 'cart';
			$additional['_query']['freegift'] = $additional['freegift'];
			$additional['_query']['apllied_rule'] = $additional['apllied_rule'];
            return $this->getProductUrl($product, $additional);
        }
        return $this->helper('checkout/cart')->getAddUrl($product, $additional);
    }
	/**
     * Retrieve Product URL using UrlDataObject
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $additional the route params
     * @return string
     */
    public function getProductUrl($product, $additional = array())
    {
        if ($this->hasProductUrl($product)) {
            if (!isset($additional['_escape'])) {
                $additional['_escape'] = true;
            }
            return $product->getUrlModel()->getUrl($product, $additional);
        }

        return '#';
    }
    
	/**
     * Check Product has URL
     *
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     */
    public function hasProductUrl($product)
    {
        if ($product->getVisibleInSiteVisibilities()) {
            return true;
        }
        if ($product->hasUrlDataObject()) {
            if (in_array($product->hasUrlDataObject()->getVisibility(), $product->getVisibleInSiteVisibilities())) {
                return true;
            }
        }

        return false;
    }
}