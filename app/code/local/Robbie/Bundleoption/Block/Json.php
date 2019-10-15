<?php

/**
* 
*/
class Robbie_Bundleoption_Block_Json extends Mage_Bundle_Block_Catalog_Product_View_Type_Bundle_Option_Checkbox
{

	public function _construct(){
		$this->setTemplate('bundleoption/json.phtml');
	}
	
	public function jsonForbaseImage()
	{

		$selectionCollectionArr = Mage::getModel('bundleoption/product')
									->jsonForbaseImage();

		

		return Mage::helper('core')
							->jsonEncode($selectionCollectionArr);
	}
}

?>