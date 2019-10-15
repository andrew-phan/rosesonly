<?php

class Ant_Advancereports_Block_Adminhtml_Hhonors_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setTemplate('advancereports/grid.phtml');
        $this->setId('hhonorsGrid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('ASC');
        //$this->setSaveParametersInSession(true);
        $this->setSubReportSize(false);
    }

    protected function _getCollectionClass() {
        return 'sales/order_collection';
    }

    protected function _prepareCollection() {
        $collections = Mage::getResourceModel('sales/order_collection')
                ->addAttributeToSelect('*')
                ->addFieldToFilter('order_hhonors_number',array('neq'=>'NULL'));
        $collections->getSelect()
                ->joinLeft(array('sfoi' => 'sales_flat_order_item'), 'sfoi.order_id = main_table.entity_id AND product_type="bundle"', array('sfoi.product_id', 'sfoi.sku'))
                ->joinLeft(array('product' => 'catalog_product_entity'), 'product.entity_id = sfoi.product_id', array('product.sku'))
                ->order('main_table.increment_id DESC');
;
        $this->setCollection($collections);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $collections = Mage::getResourceModel('sales/order_collection')
                ->addAttributeToSelect('*')
                ->addFieldToFilter('order_hhonors_number',array('neq'=>'NULL'));
        
        $this->addColumn('sku', array(
            'header' => Mage::helper('core')->__('Product purchased'),
            'index' => 'sku',
            'filter_index' => 'product.sku'
        ));
        
        /*
        $this->addColumn('order_hhonors_bonus_code', array(
            'header' => Mage::helper('reports')->__('HHonors bonus code'),
            'align' => 'left',
            'index' => 'order_hhonors_bonus_code',
            'filter_index' => 'main_table.order_hhonors_bonus_code ',
            'type' => 'text'
        ));*/
        
        $this->addColumn('order_hhonors_number', array(
            'header' => Mage::helper('reports')->__('HHonors account number'),
            'align' => 'left',
            'index' => 'order_hhonors_number',
            'filter_index' => 'main_table.order_hhonors_number ',
            'type' => 'text'
        ));
        
        $options = array();
        foreach ($collections as $c) {
            $billingAddress = $c->getBillingAddress();
            $options[$c['increment_id']] = $billingAddress->getFirstname(). ' ' .$billingAddress->getLastname();
        }
        
        $this->addColumn('customer_id', array(
            'header' => Mage::helper('reports')->__('Member contact details'),
            'align' => 'right',
            'index' => 'increment_id',
            'type' => 'options',
            'options' => $options,
        ));
        
        $this->addColumn('created_at', array(
            'header' => 'Date of transaction',
            'index' => 'created_at',
            'type' => 'date',
            'filter_index' => 'created_at',
        ));
        
        /*
        $this->addColumn('created_at', array(
            'header' => 'Number of HHonors points to be awarded',
            //'index' => 'point',
            'type' => 'text',
            //'filter_index' => 'created_at',
        ));*/
        
        
        $this->addColumn('increment_id', array(
            'header' => Mage::helper('reports')->__('Sales Order'),
            'align' => 'left',
            'index' => 'increment_id',
            'filter_index' => 'main_table.increment_id ',
            'type' => 'text'
        ));
        
        $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('sales')->__('XML'));
        return parent::_prepareColumns();
    }

    public function getRowUrl($row) {
        //return $row->getStatus();
        return;
    }

    public function _prepareMassaction() {
        $this->getMassactionBlock()->addItem('export', array(
            'label' => Mage::helper('sales')->__('Export to CSV'),
            'url' => $this->getUrl('*/*/massExport', array('_current' => true)),
        ));
    }
    /*override function getCsv() from widget/grid :write by Vanle*/
    /*                                                                  */
    /*                                                                 */
    /*                                                                 */
     /**
     * Retrieve Grid data as CSV
     *
     * @return string
     */
    public function getCsv($fields =array())
    {        
        $csv = '';
        $this->_isExport = true;
        $this->_prepareGrid();
        $this->getCollection()->getSelect()->limit();
        //$this->getCollection()->setPageSize(0);
        $this->getCollection()->setPageSize(500)->setCurPage(1);
        $this->getCollection()->load();
        $this->_afterLoadCollection();

        $data = array();
        foreach ($this->_columns as $index => $column) {            
            if(sizeof($fields)==0 || (sizeof($fields)>0 && in_array($index, $fields))){
                if (!$column->getIsSystem()) {
                    $data[] = '"'.$column->getExportHeader().'"';
                }
            }
        }
        $csv.= implode(',', $data)."\n";

        foreach ($this->getCollection() as $item) {
            $data = array();
            foreach ($this->_columns as $index => $column) {
                if(sizeof($fields)==0 || (sizeof($fields)>0 && in_array($index, $fields))){
                    if (!$column->getIsSystem()) {
                        $data[] = '"' . str_replace(array('"', '\\'), array('""', '\\\\'),
                            $column->getRowFieldExport($item)) . '"';
                    }
                }
            }
            $csv.= implode(',', $data)."\n";
        }

        if ($this->getCountTotals())
        {
            $data = array();
            foreach ($this->_columns as $column) {
                if (!$column->getIsSystem()) {
                    $data[] = '"' . str_replace(array('"', '\\'), array('""', '\\\\'),
                        $column->getRowFieldExport($this->getTotals())) . '"';
                }
            }
            $csv.= implode(',', $data)."\n";
        }

        return $csv;
    }
    /*override function getXml() from widget/grid :write by Vanle*/
    /*                                                                  */
    /*                                                                 */
    /*                                                                 */
     
    public function getXml($fields =array())
    {
        $this->_isExport = true;
        $this->_prepareGrid();
        $this->getCollection()->getSelect()->limit();
        //$this->getCollection()->setPageSize(0);
        $this->getCollection()->setPageSize(500)->setCurPage(1);
        $this->getCollection()->load();
        $this->_afterLoadCollection();
        $indexes = array();
        foreach ($this->_columns as $index =>$column) {
            if(sizeof($fields)==0 || (sizeof($fields)>0 && in_array($index, $fields))){
            if (!$column->getIsSystem()) {
                $indexes[] = $column->getIndex();
            }
        }
        }
//        var_dump($indexes);
//        exit();
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml.= '<items>';
        foreach ($this->getCollection() as $item) {           
            $xml.= $item->toXml($indexes);
        }       
        
        if ($this->getCountTotals())
        {
            $xml.= $this->getTotals()->toXml($indexes);
        }
        $xml.= '</items>';
        return $xml;
    }

}