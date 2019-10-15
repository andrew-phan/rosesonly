<?php

include_once('Mage/Adminhtml/controllers/Catalog/CategoryController.php');

class Ant_Adminhtml_Catalog_CategoryController extends Mage_Adminhtml_Catalog_CategoryController {

    /**
     * Category save
     */
    public function saveAction() {
        if (!$category = $this->_initCategory()) {
            return;
        }

        $storeId = $this->getRequest()->getParam('store');
        $refreshTree = 'false';
        if ($data = $this->getRequest()->getPost()) {
            $category->addData($data['general']);
            if (!$category->getId()) {
                $parentId = $this->getRequest()->getParam('parent');
                if (!$parentId) {
                    if ($storeId) {
                        $parentId = Mage::app()->getStore($storeId)->getRootCategoryId();
                    } else {
                        $parentId = Mage_Catalog_Model_Category::TREE_ROOT_ID;
                    }
                }
                $parentCategory = Mage::getModel('catalog/category')->load($parentId);
                $category->setPath($parentCategory->getPath());
            }

            /**
             * Process "Use Config Settings" checkboxes
             */
            if ($useConfig = $this->getRequest()->getPost('use_config')) {
                foreach ($useConfig as $attributeCode) {
                    $category->setData($attributeCode, null);
                }
            }

            /**
             * Create Permanent Redirect for old URL key
             */
            if ($category->getId() && isset($data['general']['url_key_create_redirect'])) {
                // && $category->getOrigData('url_key') != $category->getData('url_key')
                $category->setData('save_rewrites_history', (bool) $data['general']['url_key_create_redirect']);
            }

            $category->setAttributeSetId($category->getDefaultAttributeSetId());

            if (isset($data['category_products']) &&
                    !$category->getProductsReadonly()) {
                $products = array();
                parse_str($data['category_products'], $products);
                $category->setPostedProducts($products);
            }

            Mage::dispatchEvent('catalog_category_prepare_save', array(
                'category' => $category,
                'request' => $this->getRequest()
            ));

            /**
             * Proceed with $_POST['use_config']
             * set into category model for proccessing through validation
             */
            $category->setData("use_post_data_config", $this->getRequest()->getPost('use_config'));

            try {
                $validate = $category->validate();
                if ($validate !== true) {
                    foreach ($validate as $code => $error) {
                        if ($error === true) {
                            Mage::throwException(Mage::helper('catalog')->__('Attribute "%s" is required.', $category->getResource()->getAttribute($code)->getFrontend()->getLabel()));
                        } else {
                            Mage::throwException($error);
                        }
                    }
                }

                /**
                 * Check "Use Default Value" checkboxes values
                 */
                if ($useDefaults = $this->getRequest()->getPost('use_default')) {
                    foreach ($useDefaults as $attributeCode) {
                        $category->setData($attributeCode, false);
                    }
                }

                /**
                 * Unset $_POST['use_config'] before save
                 */
                $category->unsetData('use_post_data_config');
                //$productCollection = Mage::getModel('catalog/product')->getCollection();
                $productCollection = $category->getProductCollection();
                foreach ( $productCollection as $_product) {
                    //Mage::getSingleton('adminhtml/session')->addSuccess($_product->getEntityId());
                    $product = Mage::getModel('catalog/product')->load($_product->getEntityId());
                    if ($product->getTypeId()=='bundle'){
                        if ($data['general']['enable_lead_time'] == 1 && isset($data['general']['leadtime'])) {
                            //$product->setLeadTime($data['general']['leadtime']);
                            $product->setData('lead_time', $data['general']['leadtime'])
                                    ->getResource()
                                    ->saveAttribute($product, 'lead_time');
                        }
                    
                        if (isset($data['general']['delivery_note'])){
                            //$product->setDeliveryNote($data['general']['delivery_note']);
                            $product->setData('delivery_note', $data['general']['delivery_note'])
                                    ->getResource()
                                    ->saveAttribute($product, 'delivery_note');
                        }
                        
                        if (isset($data['general']['delivery_note_start_date'])){
                            //$product->setDeliveryNoteStartDate($data['general']['delivery_note_start_date']);
                            $product->setData('delivery_note_start_date', $data['general']['delivery_note_start_date'])
                                    ->getResource()
                                    ->saveAttribute($product, 'delivery_note_start_date');
                        }
                        
                        if (isset($data['general']['delivery_note_end_date'])){
                            //$product->setDeliveryNoteEndDate($data['general']['delivery_note_end_date']);
                            $product->setData('delivery_note_end_date', $data['general']['delivery_note_end_date'])
                                    ->getResource()
                                    ->saveAttribute($product, 'delivery_note_end_date');
                        }
                        
                        if (isset($data['general']['earliest_delivery_start'])){
                            //$product->setEarliestDeliveryStart($data['general']['earliest_delivery_start']);
                            $product->setData('earliest_delivery_start', $data['general']['earliest_delivery_start'])
                                    ->getResource()
                                    ->saveAttribute($product, 'earliest_delivery_start');
                        }
                        
                        if (isset($data['general']['earliest_delivery_end'])){
                            //$product->setEarliestDeliveryEnd($data['general']['earliest_delivery_end']);
                            $product->setData('earliest_delivery_end', $data['general']['earliest_delivery_end'])
                                    ->getResource()
                                    ->saveAttribute($product, 'earliest_delivery_end');
                        }
                        
                        if (isset($data['general']['earliest_delivery_date'])){
                            //$product->setEarliestDeliveryDate($data['general']['earliest_delivery_date']);
                            $product->setData('earliest_delivery_date', $data['general']['earliest_delivery_date'])
                                    ->getResource()
                                    ->saveAttribute($product, 'earliest_delivery_date');
                        }
                                            
                        if ($data['general']['earliest_delivery_time']==185){
                            //$product->setEarliestDeliveryTime(183);
                            $product->setData('earliest_delivery_time', 183)
                                    ->getResource()
                                    ->saveAttribute($product, 'earliest_delivery_time');
                        }elseif ($data['general']['earliest_delivery_time']==186){
                            //$product->setEarliestDeliveryTime(182);
                            $product->setData('earliest_delivery_time', 182)
                                    ->getResource()
                                    ->saveAttribute($product, 'earliest_delivery_time');
                        }elseif ($data['general']['earliest_delivery_time']==187){
                            //$product->setEarliestDeliveryTime(181);
                            $product->setData('earliest_delivery_time', 181)
                                    ->getResource()
                                    ->saveAttribute($product, 'earliest_delivery_time');
                        }else{
                            $product->setData('earliest_delivery_time', null)
                                    ->getResource()
                                    ->saveAttribute($product, 'earliest_delivery_time');
                        }

                        try { 
                            //$product->save();
                            //echo "Product updated";
                        }catch(Exception $ex) { 
                            $this->_getSession()->addError($ex->getMessage());
                        }                     
                        
                    }
                }

                $category->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('catalog')->__('The category has been saved with '.sizeof($productCollection).' products'));
                $refreshTree = 'true';
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage())
                        ->setCategoryData($data);
                $refreshTree = 'false';
            }
        }
        $url = $this->getUrl('*/*/edit', array('_current' => true, 'id' => $category->getId()));
        $this->getResponse()->setBody(
                '<script type="text/javascript">parent.updateContent("' . $url . '", {}, ' . $refreshTree . ');</script>'
        );
    }

}
