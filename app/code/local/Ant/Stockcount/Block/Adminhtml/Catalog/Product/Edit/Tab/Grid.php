<?php

class Ant_Stockcount_Block_Adminhtml_Catalog_Product_Edit_Tab_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId("Stockcount_Grid");
        $this->setDefaultSort("product_id");
        $this->setDefaultDir("ASC");
        $this->setSaveParametersInSession(true);
//        $this->setUseAjax(true);
    }

    protected function _prepareCollection() {
        $id = $this->getProductId();
        $product_type = $this->getProductType();
        if ($product_type == "bundle") {
            $resource = Mage::getSingleton('core/resource');
            $readConnection = $resource->getConnection('core_read');
            $query = "SELECT product_id FROM catalog_product_bundle_selection
					where parent_product_id = " . $id;
            $result = $readConnection->query($query);
            while ($row = $result->fetch()) {
                $product_id[] = $row['product_id'];
            }
            $collection = Mage::getModel("catalog/product")
                    ->getCollection()
                    ->addAttributeToSelect(array('entity_id', 'name', 'sku'))
                    ->addFieldToFilter("entity_id", array("in" => $product_id))
                    ->load();
        } else if ($product_type == "configurable") {
            $_product = Mage::getModel("catalog/product")->load($id);
            $product_id = Mage::getModel('catalog/product_type_configurable')->getUsedProductIds($_product);
            $collection = Mage::getModel("catalog/product")
                    ->getCollection()
                    ->addAttributeToSelect(array('entity_id', 'name', 'sku'))
                    ->addFieldToFilter("entity_id", array("in" => $product_id))
                    ->load();
        } else if ($product_type == "grouped") {
            $_product = Mage::getModel("catalog/product")->load($id);
            $product_id = Mage::getModel('catalog/product_type_grouped')->getAssociatedProductIds($_product, true);
            $collection = Mage::getModel("catalog/product")
                    ->getCollection()
                    ->addAttributeToSelect(array('entity_id', 'name', 'sku'))
                    ->addFieldToFilter("entity_id", array("in" => $product_id))
                    ->load();
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn("id", array(
            "header" => Mage::helper("deliverymanagement")->__("ID"),
            "align" => "center",
            "width" => "50px",
            "index" => "entity_id",
        ));
        $this->addColumn("name", array(
            "header" => Mage::helper("deliverymanagement")->__("Name"),
            "align" => "left",
            "width" => "200px",
            "index" => "name",
        ));
        $this->addColumn("sku", array(
            "header" => Mage::helper("deliverymanagement")->__("SKU"),
            "align" => "left",
            "width" => "200px",
            "index" => "sku",
        ));
        $this->addColumn('qty', array(
            'header' => Mage::helper('AdvancedStock')->__('Stock Summary'),
            'index' => 'stock_summary',
            'renderer' => 'MDN_AdvancedStock_Block_Product_Widget_Grid_Column_Renderer_StockSummary',
            'sortable' => false,
            'filter' => false,
            "width" => "300px",
        ));
        return parent::_prepareColumns();
    }

    public function getRowUrl($row) {
        return $this->getUrl("*/*/edit", array("id" => $row->getId()));
    }

    public function getProductId() {
        $id = $this->getRequest()->getParam("id");
        return $id;
    }

    public function getProductType() {
        $id = $this->getProductId();
        $_product = Mage::getModel("catalog/product")->load($id);
        return $_product->getTypeId();
    }

}