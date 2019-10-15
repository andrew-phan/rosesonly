<?php
/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 *
 * @category   MageWorx
 * @package    MageWorx_SeoSuite
 * @copyright  Copyright (c) 2011 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * SEO Suite extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoSuite
 * @author     MageWorx Dev Team
 */

class MageWorx_Adminhtml_SeosuiteController extends Mage_Adminhtml_Controller_Action {
    
    private $_product;
    
    public function applyUrlAction() {
        $this->loadLayout();
        $this->renderLayout();
    }    
    
    public function runApplyUrlAction() {
        $limit = 50;        
        $current = intval($this->getRequest()->getParam('current', 0));        
        $total = $this->_getTotalProductCount();
        $result = array();        
        if ($current<$total) {
            $this->_applyUrl($current, $limit);
            $current += $limit;            
            if ($current>=$total) {
                $current = $total;
                $result['stop'] = 1;
            }
            $result['text'] = $this->__('Total %1$s, processed %2$s records (%3$s%%)...', $total, $current, round($current*100/$total, 2));
            $result['url'] = $this->getUrl('*/*/runApplyUrl/', array('current'=>$current));
        }               
        
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
    
    protected function _getTotalProductCount() {
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        $tablePrefix = (string)Mage::getConfig()->getTablePrefix();
        $select = $connection->select()->from($tablePrefix.'catalog_product_entity', 'COUNT(*)');
        $total = $connection->fetchOne($select);        
        return intval($total);
    }
    
    
    protected function _applyUrl($from, $limit) {
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        $tablePrefix = (string)Mage::getConfig()->getTablePrefix();

        $select = $connection->select()->from($tablePrefix.'eav_entity_type')->where("entity_type_code = 'catalog_product'");
        $productTypeId = $connection->fetchOne($select);

        $select = $connection->select()->from($tablePrefix.'eav_attribute')->where("entity_type_id = $productTypeId AND (attribute_code = 'url_path')");
        $urlPathId = $connection->fetchOne($select);
        $select = $connection->select()->from($tablePrefix.'eav_attribute')->where("entity_type_id = $productTypeId AND (attribute_code = 'url_key')");
        $urlKeyId = $connection->fetchOne($select);
        
        
        $select = $connection->select()->from($tablePrefix.'catalog_product_entity')->limit($limit, $from);
        $products = $connection->fetchAll($select);
        
        
        $storeCode = Mage::app()->getRequest()->getParam('store', false);
        if ($storeCode) {
            $store = Mage::getModel('core/store')->load($storeCode);
            
            $stores = array(
                $store->getId()
            );
        } else {
            $stores = Mage::getModel('core/store')->getCollection()->load()->getAllIds();
            array_unshift($stores, 0);
        }
        
        $template = Mage::getModel('seosuite/catalog_product_template_url');
        foreach ($products as $_product) {
            foreach ($stores as $storeId){
                $this->_product = Mage::getSingleton('catalog/product')->setStoreId($storeId)->load($_product['entity_id']);
                if ($this->_product){
                    $urlKeyTemplate = (string) Mage::getStoreConfig('mageworx_seo/seosuite/product_url_key', $storeId);

                    $template->setTemplate($urlKeyTemplate)
                        ->setProduct($this->_product);

                    $urlKey = $template->process();

                    if ($urlKey == '') {
                        $urlKey = $this->_product->getName();
                    }
                    $urlKey = $this->_product->formatUrlKey($urlKey);

                    $urlSuffix = Mage::getStoreConfig('catalog/seo/product_url_suffix', $storeId);
                    
                    $select = $connection->select()->from($tablePrefix.'catalog_product_entity_varchar')->
                            where("entity_type_id = $productTypeId AND attribute_id = $urlKeyId AND entity_id = {$this->_product->getId()} AND store_id = {$storeId}");
                    $row = $connection->fetchRow($select);
                    if ($row) {
                        $connection->update($tablePrefix.'catalog_product_entity_varchar', array('value' => $urlKey), "entity_type_id = $productTypeId AND attribute_id = $urlKeyId AND entity_id = {$this->_product->getId()} AND store_id = {$storeId}");
                    } else {
                        $data = array(
                            'entity_type_id' => $productTypeId,
                            'attribute_id' => $urlKeyId,
                            'entity_id' => $this->_product->getId(),
                            'store_id' => $storeId,
                            'value' => $urlKey
                        );
                        $connection->insert($tablePrefix.'catalog_product_entity_varchar', $data);
                    }
                    
                    $select = $connection->select()->from($tablePrefix.'catalog_product_entity_varchar')->
                            where("entity_type_id = $productTypeId AND attribute_id = $urlPathId AND entity_id = {$this->_product->getId()} AND store_id = {$storeId}");
                    $row = $connection->fetchRow($select);
                    if ($row) {
                        $connection->update($tablePrefix.'catalog_product_entity_varchar', array('value' => $urlKey . $urlSuffix), "entity_type_id = $productTypeId AND attribute_id = $urlPathId AND entity_id = {$this->_product->getId()} AND store_id = {$storeId}");
                    } else {
                        $data = array(
                            'entity_type_id' => $productTypeId,
                            'attribute_id' => $urlPathId,
                            'entity_id' => $this->_product->getId(),
                            'store_id' => $storeId,
                            'value' => $urlKey . $urlSuffix
                        );
                        $connection->insert($tablePrefix.'catalog_product_entity_varchar', $data);
                    }
                }
            }
        }

        Mage::getModel('catalog/url')->refreshRewrites();
    }
    
    
}