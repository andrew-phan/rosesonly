<?php

//deprecated
class MDN_Quotation_Block_Bundle_Checkout_Cart_Item_Renderer extends Mage_Bundle_Block_Checkout_Cart_Item_Renderer {

    public function getOptionListPrice()
    {
        return Mage::helper('bundle/catalog_product_configuration')->getOptionsPrice($this->getItem());
    }

}