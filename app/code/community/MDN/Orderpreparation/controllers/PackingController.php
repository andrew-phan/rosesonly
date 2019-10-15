<?php

class MDN_Orderpreparation_PackingController extends Mage_Adminhtml_Controller_Action {

    /**
     * Main screen for packing
     */
    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function OrderInformationAction() {
        //init var
        $response = array();
        $response['error'] = false;
        $response['message'] = '';

        $barcode = $this->getRequest()->getParam('barcode');

        try {
            //get order
            $order = Mage::getModel('sales/order')->load($barcode, 'increment_id');
            if (!$order->getId())
                throw new Exception($this->__('Unable to find order #%s', $barcode));

            //check that order is not shipped
            $orderToPrepare = Mage::getModel('Orderpreparation/ordertoprepare')->load($order->getId(), 'order_id');
            if ($orderToPrepare->getshipment_id())
                throw new Exception($this->__('This order is already packed !'));

            //return order information
            $block = $this->getLayout()->createBlock('Orderpreparation/Packing_Products');
            $block->setTemplate('Orderpreparation/Packing/Products.phtml');
            $block->setOrder($order);
            $orderInformation = $block->toHtml();
            $response['order_html'] = $orderInformation;
            $response['order_id'] = $order->getId();

            $response['products_json'] = $this->getProductJson($order);
        } catch (Exception $ex) {
            $response['error'] = true;
            $response['message'] = $ex->getMessage();
        }


        //return response
        $response = Zend_Json::encode($response);
        $this->getResponse()->setBody($response);
    }

    /**
     * return product json array
     * @param <type> $order
     */
    protected function getProductJson($order) {
        $array = array();

        $orderId = $order->getId();
        $products = Mage::getModel('Orderpreparation/ordertoprepare')->GetItemsToShip($orderId);
        foreach ($products as $product) {
            if ($this->productManageStock($product))
            {
                $item = array();
                $item['name'] = $product->getSalesOrderItem()->getName();
                $item['id'] = $product->getId();
                $item['qty_scanned'] = 0;
                $item['qty'] = $product->getqty();
                $item['barcode'] = Mage::helper('AdvancedStock/Product_Barcode')->getBarcodeForProduct($product->getproduct_id());

                $array[] = $item;
            }
        }

        return $array;
    }

    /**
     * return true if product manage stocks
     * 
     * @param type $orderToPrepareItem
     * @return type 
     */
    public function productManageStock($orderToPrepareItem) {
        $productId = $orderToPrepareItem->getproduct_id();
        $product = Mage::getModel('catalog/product')->load($productId);
        return $product->getStockItem()->getManageStock();
    }

    /**
     * Commit packing 
     */
    public function CommitAction() {
        $orderId = $this->getRequest()->getPost('order_id');

        try {

            //Create shipment
            if (Mage::getStoreConfig('orderpreparation/packing/create_shipment_on_commit')) {
                $order = Mage::getModel('sales/order')->load($orderId);
                $preparationWarehouseId = mage::helper('Orderpreparation')->getPreparationWarehouse();
                $operatorId = mage::helper('Orderpreparation')->getOperator();
                Mage::helper('Orderpreparation/Shipment')->CreateShipment($order, $preparationWarehouseId, $operatorId);
            }

            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Packing commited'));
        } catch (Exception $ex) {
            Mage::getSingleton('adminhtml/session')->addError($this->__($ex->getMessage()));
        }

        //redirect
        $this->_redirect('OrderPreparation/Packing/');
    }

}
