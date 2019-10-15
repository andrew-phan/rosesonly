<?php

/**
  pradeep.kumarrcs67@gmail.com

 */
class Ant_Catalog_Block_Product_List_Toolbar extends Mage_Catalog_Block_Product_List_Toolbar {

    public function getAvailableOrders() {
        $this->_availableOrder = array();
        $this->addOrderToAvailableOrders('created_at', 'Sort by');
        $this->addOrderToAvailableOrders('created_at', 'New Arrivals');
        $this->addOrderToAvailableOrders('sku', 'SKU');
        $this->addOrderToAvailableOrders('price', 'Price');        
        $this->setDefaultOrder('sku');
        $this->setDefaultDirection('asc');
        return $this->_availableOrder;
    }

}