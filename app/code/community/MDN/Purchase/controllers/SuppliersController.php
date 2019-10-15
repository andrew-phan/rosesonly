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
//Controlleur pour la gestion des suppliers
class MDN_Purchase_SuppliersController extends Mage_Adminhtml_Controller_Action {

    public function indexAction() {
        
    }

    /**
     * 
     *
     */
    public function ListAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     *  
     *
     */
    public function NewAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * 
     *
     */
    public function CreateAction() {
        $Supplier = mage::getModel('Purchase/Supplier');

        //original code begin
        /*
          $Supplier->setsup_name($this->getRequest()->getParam('sup_name'));
          $Supplier->save();
          //confirm & redirect
          Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Supplier Created'));
          $this->_redirect('Purchase/Suppliers/Edit/sup_id/' . $Supplier->getId());
         */
        //original code end
        
        //edited code begin
        $collection_of_suppliers = Mage::getModel('Purchase/Supplier')
                ->getCollection();
        $collection_of_suppliers->addFieldToFilter('sup_name', $this->getRequest()->getParam('sup_name'));

        if ($collection_of_suppliers->getFirstItem()->getData()) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Cannot create because the name is the same'));
            $this->_redirect('Purchase/Suppliers/New/');
        } else {
            $Supplier->setsup_name($this->getRequest()->getParam('sup_name'));
            $Supplier->save();
            //confirm & redirect
            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Supplier Created'));
            $this->_redirect('Purchase/Suppliers/Edit/sup_id/' . $Supplier->getId());
        }
        //edited code end
    }

    /**
     *
     *
     */
    public function EditAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Save supplier information
     *
     */
    public function SaveAction() {
        //load supplier & infos
        $Supplier = Mage::getModel('Purchase/Supplier')->load($this->getRequest()->getParam('sup_id'));
        $currentTab = $this->getRequest()->getParam('current_tab');
        $data = $this->getRequest()->getPost();

        //original code begin
        /*
        //customize datas
        if (isset($data['sup_discount_level']))
            $data['sup_discount_level'] = str_replace(',', '.', $data['sup_discount_level']);

        //save datas
        foreach ($data as $key => $value) {
            $Supplier->setData($key, $value);
        }
        $Supplier->save();

        //confirm & redirect
        Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Supplier Saved'));
        */
        //original code end
        
        //edited code begin
        $collection_of_suppliers = Mage::getModel('Purchase/Supplier')
                ->getCollection();
        $collection_of_suppliers->addFieldToFilter('sup_name', $data['sup_name']);
        $collection_of_suppliers->addFieldToFilter('sup_id', array('neq' => $Supplier->getId()));

        if ($collection_of_suppliers->getFirstItem()->getData()) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Cannot save because the name is the same'));
        } else {
            //customize datas
            if (isset($data['sup_discount_level']))
                $data['sup_discount_level'] = str_replace(',', '.', $data['sup_discount_level']);

            //save datas
            foreach ($data as $key => $value) {
                $Supplier->setData($key, $value);
            }
            $Supplier->save();

            //confirm & redirect
            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Supplier Saved'));
        }
        //edited code end

        $this->_redirect('Purchase/Suppliers/Edit', array('sup_id' => $Supplier->getId(), 'tab' => $currentTab));
    }

    /**
     * Return supplier's orders grid
     */
    public function AssociatedOrdersGridAction() {
        $this->loadLayout();
        $supId = $this->getRequest()->getParam('sup_id');
        $Block = $this->getLayout()->createBlock('Purchase/Supplier_Edit_Tabs_Orders');
        $Block->setSupplierId($supId);
        $this->getResponse()->setBody($Block->toHtml());
    }

    /**
     * Return supplier's products grid
     */
    public function ProductsGridAction() {
        $this->loadLayout();
        $supId = $this->getRequest()->getParam('sup_id');
        $Block = $this->getLayout()->createBlock('Purchase/Supplier_Edit_Tabs_Products');
        $Block->setSupplierId($supId);
        $this->getResponse()->setBody($Block->toHtml());
    }

    /**
     * 
     */
    public function SynchronizeWithManufacturersAction() {
        try {
            $result = Mage::helper('purchase/supplier')->synchronizeManufacturersAndSuppliers();
            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('%s suppliers created', $result));
        } catch (Exception $ex) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('An error occured : %s', $ex->getMessage()));
        }
        $this->_redirect('adminhtml/system_config/edit', array('section' => 'purchase'));
    }

}