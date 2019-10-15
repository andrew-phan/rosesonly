<?php

class Ant_Adminhtml_Block_Sales_Order_Create_Search_Grid extends Mage_Adminhtml_Block_Sales_Order_Create_Search_Grid {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Prepare columns
     *
     * @return Mage_Adminhtml_Block_Sales_Order_Create_Search_Grid
     */
    protected function _prepareColumns() {
        $this->addColumn('image', array(
            'header' => Mage::helper('catalog')->__('Image'),
            'align' => 'left',
            'index' => 'thumbnail',
            'width' => '70',
            'renderer' => 'Ant_Adminhtml_Block_Widget_Grid_Column_Renderer_Picture'
        ));

        return parent::_prepareColumns();
    }

    /**
     * Prepare collection to be displayed in the grid
     *
     * @return Mage_Adminhtml_Block_Sales_Order_Create_Search_Grid
     */
    protected function _prepareCollection() {
        $attributes = Mage::getSingleton('catalog/config')->getProductAttributes();
        /* @var $collection Mage_Catalog_Model_Resource_Product_Collection */
        $collection = Mage::getModel('catalog/product')->getCollection();
        $vs_Now = Mage::getSingleton('core/date')->gmtDate();
        $collection
                ->addAttributeToFilter('type_id', array('eq' => 'bundle'))
                ->addFieldToFilter('start_date', array(array("lt" => $vs_Now), array("eq" => $vs_Now), array("null" => true)))
                ->addFieldToFilter('end_date', array(array("gt" => $vs_Now), array("eq" => $vs_Now), array("null" => true)))
                ->setStore($this->getStore())
                ->addAttributeToSelect($attributes)
                ->addAttributeToSelect('sku')
                ->addStoreFilter()
                ->addAttributeToFilter('type_id', array_keys(
                                Mage::getConfig()->getNode('adminhtml/sales/order/create/available_product_types')->asArray()
                        ))
                ->addAttributeToSelect('gift_message_available');

        Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($collection);

        $this->setCollection($collection);
        //return $collection;
        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }

}