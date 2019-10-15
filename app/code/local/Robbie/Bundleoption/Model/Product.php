<?php

/**
* 
*/
class Robbie_Bundleoption_Model_Product extends Mage_Catalog_Model_Product
{
	
	private $_imageWidth = 80;
	private $_imageHeight = 80;
	/*
	*	Initialize Parent Resources	
	*/

	protected function _construct()
	{
		
		$this->_imageHeight = Mage::getStoreConfig('bundleoption_section_one/image_size_group/image_height');
		parent::_construct();

	}

	protected function _getImageWidth()
	{
		$width = Mage::getStoreConfig('bundleoption_section_one/image_size_group/image_width');
		if(!is_null($width))
		{
			return $width;
		}

		return $this->_imageWidth;	
	}

	protected function _getImageHeight()
	{
		$height = Mage::getStoreConfig('bundleoption_section_one/image_size_group/image_width');
		if(!is_null($height))
		{
			return $height;
		}

		return $this->_imageHeight;	
	}	


	protected function _getProId($sku){
		$product = Mage::getModel('catalog/product')->loadByAttribute('sku',$sku);
		return $product->getId();
	}

	protected function _getOptionType($option_id = array()){

		$type = NULL;	
		$current_product = Mage::registry('current_product');

		$optionCollection = $current_product->getTypeInstance(true)->getOptionsByIds(
				            array($option_id),
				            $current_product
				        )->getData();

		foreach ($optionCollection as $options) {
				$type = $options['type'];
		}

		return $type;

	}



	protected function _getProductData($product_id){

		$product = Mage::getModel('catalog/product')
				->load($product_id);

		$image = Mage::helper('catalog/image')
				->init($product,'image')
				->resize($this->_getImageWidth(),$this->_getImageHeight())->__toString();

		return $image;
	}
	protected function _getBasechangeValue(){

		return Mage::getStoreConfig('bundleoption_section_one/group_1/field_basechange_onselect_type');

	}

	public function jsonData($type = '') {	

      	$options    = array();      
      	$bundled_product = Mage::registry('current_product');

		    $selectionCollection = $bundled_product->getTypeInstance(true)->getSelectionsCollection(
		        $bundled_product->getTypeInstance(true)->getOptionsIds($bundled_product), $bundled_product
		    );

		    $bundled_items = array();		    
		    foreach($selectionCollection as $option)
		    {
		    	if($this->_getOptionType($option->option_id) === $type){
			        $bundled_items[$option->selection_id] = $this->_getProductData($option->product_id);
		    	}

		    }            
   		
		return $bundled_items;    
	}

	public function jsonForbaseImage(){

		$options    = array();      
      	$bundled_product = Mage::registry('current_product');


		$optionCollection = $bundled_product->getTypeInstance(true)->getOptionsCollection($bundled_product);    
		
		$optTypeSelect = array();
		foreach ($optionCollection->getData() as $option) 
		{
			if($option['type'] == 'select')
			{
				$optTypeSelect[$option['option_id']] = $option['position'];
			}	
		}

		if(!empty($optTypeSelect))
		{
			if($this->_getBasechangeValue())
			{
			   $optTypeSelect = (array_keys($optTypeSelect, min($optTypeSelect))); 

			}else{
				$optTypeSelect = array_keys($optTypeSelect);
			}			
		}	
		

	    $selectionCollection = $bundled_product->getTypeInstance(true)
	    						->getSelectionsCollection(
	        							$optTypeSelect, $bundled_product
	    							);
		

	    $bundled_items = array();						
	    foreach($selectionCollection as $option)
	    {	    	
		    $bundled_items[$option->selection_id] = $this->_getProductData($option->product_id,265); 	

	    }

	     return $bundled_items;							

	}



}

?>
