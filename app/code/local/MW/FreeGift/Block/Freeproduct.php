<?php
class MW_FreeGift_Block_Freeproduct extends Mage_Core_Block_Template
{	
    public function getFreeCatalogGifts()
    {
    	$currentProduct = Mage::registry('current_product')?Mage::registry('current_product'):false;
    	if(!$currentProduct) $currentProduct = $this->getRequest()->getParam('id')?Mage::getModel('catalog/product')->load($this->getRequest()->getParam('id')):false;
    	
    	if($currentProduct)
    	{
    		//return Mage::getSingleton('freegift/gift')->init()->getFreeGifts($currentProduct);
    		return Mage::getModel('freegift/product')->init($currentProduct)->getFreeGifts();
    	}
    	return false;
    }
    
    public function _toHtml()
    {
    	if(!Mage::getStoreConfig('freegift/config/enabled')) return '';
    	if(!sizeof($this->getFreeCatalogGifts())) return '';
    	$html = $this->renderView();
        return $html;
    }
}