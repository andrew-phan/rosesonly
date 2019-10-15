<?php
/**
* 
*/
require_once(Mage::getModuleDir('controllers','Mage_Catalog').DS.'ProductController.php');

class Robbie_Bundleoption_ProductController extends Mage_Catalog_ProductController
{
	
	public function viewAction()
	{
		$this->loadLayout('PRODUCT_TYPE_bundle');
	}

	public function bundleAjaxAction()
	{
		if (!$this->getRequest()->isXmlHttpRequest()) 
		{
            $this->_redirect('/');
        }

        if ($product = $this->_initProduct()) 
        {
            $this->getResponse()
                    ->setBody($this->getLayout()
                        ->createBlock('bundleoption/catalog_product_view_bundleAjax')
                        ->setProduct($product)
                        ->toHtml());
        } 
        else 
        {
            echo Mage::helper('catalog')->__('Product not found');
        }
	}
}

?>