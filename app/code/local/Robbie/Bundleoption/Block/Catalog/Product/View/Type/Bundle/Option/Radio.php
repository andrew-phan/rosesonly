<?php 
/**
* 
*/
class Robbie_Bundleoption_Block_Catalog_Product_View_Type_Bundle_Option_Radio extends Mage_Bundle_Block_Catalog_Product_View_Type_Bundle_Option_Radio
{
	
	public function _construct(){

		$this->setTemplate('bundleoption/radio.phtml');
	}
	
	public function jsonData($selection_id = null,$json = true)
	{
		$type = 'radio';
		$selectionCollectionArr = Mage::getModel('bundleoption/product')
									->jsonData($type);

		if($json == true){

			if($selection_id == null){
				$jsonData = Mage::helper('core')
							->jsonEncode($selectionCollectionArr);
			}else{
				$jsonData = Mage::helper('core')
							->jsonEncode($selectionCollectionArr[$selection_id]);
			}	
		}else{
			if($selection_id == null){
				$jsonData = $selectionCollectionArr;
			}else{
				$jsonData = $selectionCollectionArr[$selection_id];
			}			
		}		

		return $jsonData;
	}

	public function getPlaceholder() {	
		$imageHelper = Mage::helper('catalog/image');
		$image = $imageHelper->init(Mage::getModel('catalog/product'), 'small_image')
					->resize(150,150)
					->__toString();

		return $image;
	}

	public function getBackgroundColor() {

		return Mage::getStoreConfig('bundleoption_section_one/group_1/field_background_color');
	}	

}

?>