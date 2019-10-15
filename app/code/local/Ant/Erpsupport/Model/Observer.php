<?php

class Ant_Erpsupport_Model_Observer {

//    public function getProductSupplier(Varien_Event_Observer $observer) {
//        $event = $observer->getEvent();
//        $productId = $event->getProductid();
//        $supplierId = $event->getSupplierid();
//        $whId = $event->getWhid();
//        if ($supplierId == null && $whId == null) {
//            $favWh = Mage::getModel('AdvancedStock/Warehouse')
//                    ->getCollection()
//                    ->addFieldToFilter('product_id', $productId)
//                    ->addFieldToFilter('pps_quantity_product', array('gt' => 0))
//                    ->setOrder('is_favorite_warehouse', 'DESC')
//                    ->getFirstItem();
//            $item = Mage::getModel('Purchase/ProductSupplier')
//                    ->getCollection()
//                    ->addFieldToFilter('pps_product_id', $productId)
//                    ->addFieldToFilter('pps_quantity_product', array('gt' => 0))
//                    ->addFieldToFilter('pps_wh_num', $favWh)
//                    ->setOrder('pps_is_default_supplier', 'DESC')
//                    ->getFirstItem();
//        } if ($supplierId == null && $whId != null) {
//            $item = Mage::getModel('Purchase/ProductSupplier')->getCollection()
//                    ->addFieldToFilter('pps_product_id', $productId)
//                    ->addFieldToFilter('pps_wh_num', $whId)
//                    ->addFieldToFilter('pps_quantity_product', array('gt' => 0))
//                    ->setOrder('pps_is_default_supplier', 'DESC')
//                    ->getFirstItem();
//        } else {
//            $item = Mage::getModel('Purchase/ProductSupplier')->getCollection()
//                    ->addFieldToFilter('pps_product_id', $productId)
//                    ->addFieldToFilter('pps_supplier_num', $supplierId)
//                    ->addFieldToFilter('pps_quantity_product', array('gt' => 0))
//                    ->addFieldToFilter('pps_wh_num', $whId)
//                    ->getFirstItem();
//        }
//        return $item;
//    }

    public function product_supplier_sub(Varien_Event_Observer $observer) {
        $event = $observer->getEvent();
        $qty = $event->getQty();
        $productId = $event->getProductid();
        //$supplierId = $event->getSupplierid();
        $whId = $event->getWhid();
        //echo $productId.' - '.$supplierId.' - '.$whId;
        /* if($supplierId != null && $whId != null){
          $item = Mage::getModel('Purchase/ProductSupplier')->getCollection()
          ->addFieldToFilter('pps_product_id', $productId)
          ->addFieldToFilter('pps_supplier_num', $supplierId)
          ->addFieldToFilter('pps_wh_num', $whId)
          ->getFirstItem();
          } else */
        //if ($supplierId == null && $whId != null) {
        if ($whId != null) {
            // Update vao supplier mac dinh cua warehouse do, 
            // neu ko co supplier mac dinh thi se update vao cai dau tien
            $item = Mage::getModel('Purchase/ProductSupplier')->getCollection()
                    ->addFieldToFilter('pps_product_id', $productId)
                    ->addFieldToFilter('pps_wh_num', $whId)
                    ->addFieldToFilter('pps_quantity_product', array('gt' => 0))
                    ->setOrder('pps_is_default_supplier', 'DESC')
                    ->getFirstItem();
        } else {
            // Phuoc's code: lay warehouse mac dinh, tam thoi van chua biet nen lay tam favorite warehouse
            $favWh = Mage::getModel('AdvancedStock/Warehouse')
                    ->getCollection()
                    ->addFieldToFilter('product_id', $productId)
                    ->setOrder('is_favorite_warehouse', 'DESC')
                    ->getFirstItem();
            /////////
            $item = Mage::getModel('Purchase/ProductSupplier')
                    ->getCollection()
                    ->addFieldToFilter('pps_product_id', $productId)
                    ->addFieldToFilter('pps_wh_num', $favWh)
                    ->addFieldToFilter('pps_quantity_product', array('gt' => 0))
                    ->setOrder('pps_is_default_supplier', 'DESC')
                    ->getFirstItem();
        }

        $defqty = $item->getpps_quantity_product();
        if ($defqty >= $qty) {
            $item->setpps_quantity_product($defqty - $qty);
            $item->save();
        } else {
            $item->setpps_quantity_product(0);
            $item->save();
            Mage::dispatchEvent('product_supplier_sub', array('productid' => $productId,
                'qty' => $qty - $defqty,
                'whid' => $whId));
        }
    }

    public function product_supplier_add(Varien_Event_Observer $observer) {
        $event = $observer->getEvent();
        $qty = $event->getQty();
        $productId = $event->getProductid();
        $supplierId = $event->getSupplierid();
        $whId = $event->getWhid();
        //echo $productId.' - '.$supplierId.' - '.$whId;
        if ($supplierId != null && $whId != null) {
            $item = Mage::getModel('Purchase/ProductSupplier')->getCollection()
                    ->addFieldToFilter('pps_product_id', $productId)
                    ->addFieldToFilter('pps_supplier_num', $supplierId)
                    ->addFieldToFilter('pps_wh_num', $whId);
                    //->getFirstItem();
        } else if ($supplierId == null && $whId != null) {
            // Update vao supplier mac dinh cua warehouse do, 
            // neu ko co supplier mac dinh thi se update vao cai dau tien
            $item = Mage::getModel('Purchase/ProductSupplier')->getCollection()
                    ->addFieldToFilter('pps_product_id', $productId)
                    ->addFieldToFilter('pps_wh_num', $whId)
                    ->setOrder('pps_is_default_supplier', 'DESC');
                    //->getFirstItem();
        } else {
            // Phuoc's code: lay warehouse mac dinh, tam thoi van chua biet nen lay tam favorite warehouse
            $favWh = Mage::getModel('AdvancedStock/Warehouse')
                    ->getCollection()
                    ->addFieldToFilter('product_id', $productId)
                    ->setOrder('is_favorite_warehouse', 'DESC')
                    ->getFirstItem();
            /////////
            $item = Mage::getModel('Purchase/ProductSupplier')
                    ->getCollection()
                    ->addFieldToFilter('pps_product_id', $productId)
                    ->addFieldToFilter('pps_wh_num', $favWh)
                    ->setOrder('pps_is_default_supplier', 'DESC');
                    //->getFirstItem();
        }
        if(count($item) == 0){ //|| $item->getPps_product_id() == 0) {
            $item = Mage::getModel('Purchase/ProductSupplier')
                ->setPps_product_id($productId)
                ->setPps_supplier_num($supplierId)
                ->setPps_wh_num($whId)
                ->setpps_quantity_product(0);
        } else {
            $item = $item->getFirstItem();
        }
        $defqty = $item->getpps_quantity_product();
        $item->setPps_quantity_product($defqty + $qty);
        $item->save();
    }

}
