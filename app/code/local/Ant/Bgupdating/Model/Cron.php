<?php

class Ant_Bgupdating_Model_Cron {
    
    public function checkBestSellerProduct() {
        
        Mage::log('start checking best seller product');
        //get currently best seller products
        $currentBestSellerProducts = Mage::getModel('catalog/product')
                ->getCollection()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('is_best_seller',1);
        
        //calculate new best seller products
        $bestSellingProductNum = 5;
        $newBestSellerProducts = Mage::getResourceModel('reports/product_collection')
                ->addAttributeToSelect('*')
                ->addOrderedQty()
                ->setOrder('ordered_qty', 'desc')
                ->setPageSize($bestSellingProductNum)
                ->addAttributeToFilter('ordered_qty',array('gt'=>0))
                ->load();

        //mark new best seller products if not yet
        $bestSellerProductIds = array();
        foreach ($newBestSellerProducts as $product) {
            array_push($bestSellerProductIds, $product->getId());
            
            if($product->getIsBestSaller() == 0)
            {
                Mage::log('Mark: '.$product->getId());
                $product->setIsBestSeller(1);
                $product->save();
            } 
        }
        
        //unmark product is not best seller any more
        foreach ($currentBestSellerProducts as $product) {
            
            if(!in_array($product->getId(), $bestSellerProductIds))
            {
                Mage::log('Unmark: '.$product->getId());
                $product->setIsBestSeller(0);
                $product->save();
            }
        }
    }
}