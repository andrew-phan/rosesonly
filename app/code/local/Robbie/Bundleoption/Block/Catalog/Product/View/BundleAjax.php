<?php  
/**
* 
* @author : RkD aka ROBIN
* @email  : rkd711@gmail.com
* 
*/
class Robbie_Bundleoption_Block_Catalog_Product_View_BundleAjax extends Mage_Catalog_Block_Product
{
    
    private $product;

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('bundleoption/bundleAjax.phtml');
    }

    protected function _toHtml() {
        return parent::_toHtml();
    }
    
    public function setProduct($product) {
        $this->product = $product;
        return $this;
    }
    
    public function getProduct() {
        return $this->product;
    }

    
    public function getReviewsSummaryHtml()
    {
        return
            $this->getLayout()->createBlock('rating/entity_detailed')
                ->setEntityId($this->getProduct()->getId())
                ->toHtml()
            
            ;
    }

    public function getProductImages()
    {
        return
            $this->getLayout()->createBlock('catalog/product_view_media')
                ->setEntityId($this->getProduct()->getId())
                ->setTemplate('bundleoption/media.phtml')
                ->toHtml()
            
            ;
    }    
}
?>
