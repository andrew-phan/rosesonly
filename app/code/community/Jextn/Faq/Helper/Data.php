<?php

class Jextn_Faq_Helper_Data extends Mage_Core_Helper_Abstract
{
    
	const XML_PATH_TITLE   = 'faq/faq/title';



	public function getFAQTitle()
	{
		if(trim(Mage::getStoreConfig(self::XML_PATH_TITLE))==''){
			$titlefaq = $this->__('FAQ');
		} else {
			$titlefaq = Mage::getStoreConfig(self::XML_PATH_TITLE);
		}
		return $titlefaq;
	} 
}