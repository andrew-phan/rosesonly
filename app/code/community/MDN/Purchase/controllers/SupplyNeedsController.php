<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright  Copyright (c) 2009 Maison du Logiciel (http://www.maisondulogiciel.com)
 * @author : Olivier ZIMMERMANN
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_Purchase_SupplyNeedsController extends Mage_Adminhtml_Controller_Action {

    public function indexAction() {

    }

    /**
     * 
     *
     */
    public function StatsAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Display grid
     *
     */
    public function GridAction() {
        $this->loadLayout();
        
        $warehouseId = $this->getRequest()->getParam('warehouse');
        Mage::helper('purchase/SupplyNeeds')->setCurrentWarehouse($warehouseId);

        $block = $this->getLayout()->createBlock('Purchase/SupplyNeeds_Grid');
        $block->setTemplate('Purchase/SupplyNeeds/Grid.phtml');
        $this->getLayout()->getBlock('content')->append($block);

        $this->renderLayout();
    }

    /**
     * Return supply needs grid in ajax
     */
    public function AjaxGridAction() {
        $poNum = $this->getRequest()->getParam('po_num');
        $mode = $this->getRequest()->getParam('mode');

        $this->loadLayout();
        $block = $this->getLayout()->createBlock('Purchase/SupplyNeeds_Grid');
        $block->setMode($mode, $poNum);
        $this->getResponse()->setBody($block->toHtml());
    }

    /**
     * Create a purchase order and add products
     *
     */
    public function CreatePurchaseOrderAction() {

        //init vars
        $data = $this->getRequest()->getPost('supply_needs_log');
        $supId = $this->getRequest()->getPost('sup_id');

        //convert data
        $supplyNeeds = array();
        $data = explode(';', $data);
        foreach ($data as $item) {
            $t = explode('=', $item);
            if (count($t) == 2) {
                $snId = str_replace('qty_', '', $t[0]);
                $qty = $t[1];
                if ($qty > 0)
                    $supplyNeeds[$snId] = $qty;
            }
        }

        //create order
        $order = mage::getModel('Purchase/Order')
                        ->setpo_sup_num($supId)
                        ->setpo_date(date('Y-m-d'))
                        ->setpo_currency(Mage::getStoreConfig('purchase/purchase_order/default_currency'))
                        ->setpo_tax_rate(Mage::getStoreConfig('purchase/purchase_order/default_shipping_duties_taxrate'))
                        ->setpo_order_id(mage::getModel('Purchase/Order')->GenerateOrderNumber())
                        ->save();

        //add products
        foreach ($supplyNeeds as $productId => $qty) {

            try {
                $order->AddProduct($productId, $qty);
            } catch (Exception $ex) {
                Mage::getSingleton('adminhtml/session')->addError($ex->getMessage());
            }
        }

        //confirme
        Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Order successfully Created'));
        $this->_redirect('Purchase/Orders/Edit', array('po_num' => $order->getId()));
    }

    /**
     * Create purchase order from stats
     */
    public function CreatePoFromStatsAction() {
        //get datas
        $supplierId = $this->getRequest()->getParam('sup_id');
        $statuses = explode(',', $this->getRequest()->getParam('status'));

        //create PO
        $po = mage::helper('purchase/Order')->createNewOrder($supplierId);

        //get supply needs
        foreach ($statuses as $status) {
            $supplyNeeds = mage::getModel('Purchase/SupplyNeeds')
                            ->getCollection()
                            ->addFieldToFilter('sn_status', $status)
                            ->addFieldToFilter('sn_suppliers_ids', array('like' => '%' . $supplierId . ',%'));

            foreach ($supplyNeeds as $supplyNeed) {
                $qty = $supplyNeed->getsn_needed_qty();
                $productId = $supplyNeed->getsn_product_id();
                try {
                    $po->AddProduct($productId, $qty);
                } catch (Exception $ex) {
                    Mage::getSingleton('adminhtml/session')->addError($ex->getMessage());
                }
            }
        }


        //confirm and redirect to PO
        Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Order successfully Created'));
        $this->_redirect('Purchase/Orders/Edit', array('po_num' => $po->getId()));
    }

    /**
     * Update Prefered stock level
     */
    public function updatePreferedStockLevelAction() {
        //get product ids
        $productIds = mage::helper('AdvancedStock/Product_Base')->getProductIds();

        //create backgroundtask group
        $taskGroup = 'update_prefered_stock_level';
        mage::helper('BackgroundTask')->AddGroup($taskGroup, mage::helper('purchase')->__('Update prefered stock level'), 'Purchase/SupplyNeeds/Grid');

        //plan tasks
        foreach ($productIds as $productId) {
            //add tasks to group
            mage::helper('BackgroundTask')->AddTask('Update warning stock level for product #' . $productId,
                    'purchase/SupplyNeeds',
                    'updatePreferedStockLevel',
                    $productId,
                    $taskGroup
            );
        }

        //execute task group
        mage::helper('BackgroundTask')->ExecuteTaskGroup($taskGroup);
    }

    /**
     * Import prefered stock levels
     */
    public function ImportPreferedStockLevelsAction() {
        try {
            //load file
            $uploader = new Varien_File_Uploader('file');
            $uploader->setAllowedExtensions(array('txt', 'csv'));
            $path = Mage::app()->getConfig()->getTempVarDir() . '/import/';
            $uploader->save($path);

            if ($uploadFile = $uploader->getUploadedFileName()) {
                //load file
                $filePath = $path . $uploadFile;
                $content = file($filePath);

                //process file
                $helper = mage::helper('purchase/PreferedStockLevel');
                $result = $helper->import($content);

                //confirm & redirect
                Mage::getSingleton('adminhtml/session')->addSuccess($result);
                $this->_redirect('Purchase/SupplyNeeds/Grid');
            }
            else
                throw new Exception('Unable to load file');
        } catch (Exception $ex) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('An error occured : %s', $ex->getMessage()));
            $this->_redirect('Purchase/SupplyNeeds/Grid');
        }
    }

}