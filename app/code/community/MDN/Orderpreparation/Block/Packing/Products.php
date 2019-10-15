<?php


class MDN_OrderPreparation_Block_Packing_Products extends Mage_Core_Block_Template {

    public function getProducts()
    {
        $orderId = $this->getOrder()->getId();
        $products = Mage::getModel('Orderpreparation/ordertoprepare')->GetItemsToShip($orderId);
        return $products;
    }

    public function getProductImageUrl($orderToPrepareItem)
    {
        $productId = $orderToPrepareItem->getproduct_id();
        $product = Mage::getModel('catalog/product')->load($productId);

        if ($product->getSmallImage()) {
            return Mage::getBaseUrl('media') . DS . 'catalog' . DS . 'product' . $product->getSmallImage();
        } else {
            //try to find image from configurable product
            $configurableProduct = Mage::helper('AdvancedStock/Product_ConfigurableAttributes')->getConfigurableProduct($product);
            if ($configurableProduct) {
                if ($configurableProduct->getSmallImage()) {
                    return Mage::getBaseUrl('media') . DS . 'catalog' . DS . 'product' . $configurableProduct->getSmallImage();
                }
            }
        }

        return '';

    }

    /**
     * return true if product manage stocks
     * 
     * @param type $orderToPrepareItem
     * @return type 
     */
    public function productManageStock($orderToPrepareItem)
    {
        $productId = $orderToPrepareItem->getproduct_id();
        $product = Mage::getModel('catalog/product')->load($productId);
        return $product->getStockItem()->getManageStock();
    }

}