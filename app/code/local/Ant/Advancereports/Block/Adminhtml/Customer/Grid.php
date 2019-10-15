<?php

class Ant_Advancereports_Block_Adminhtml_Customer_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {        
        parent::__construct();
        $this->setTemplate('advancereports/grid.phtml');
        $this->setId('customerGrid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setSubReportSize(false);
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('sales/order')->getCollection()
                ->addAttributeToSort('entity_id', 'DESC')
                 ->addAttributeToSelect('entity_id')
                  ->addAttributeToSelect('customer_id')
                  ->addAttributeToSelect('increment_id')
                  ->addAttributeToSelect('status') 
                ->addFieldToFilter('customer_id', array('neq' => 'NULL'));


        $collection->getSelect()
                ->joinLeft(array('a' => 'ant_latepayment'), 'a.order_id = main_table.entity_id', 'a.latestatus');


        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $collections = Mage::getModel('sales/order')
                ->getCollection()
                ->addAttributeToSelect('*');

                /*
        $options = array();
        foreach ($collections as $collection) {
            $options[$collection['customer_id']] = $collection['customer_firstname'] . ' ' . $collection['customer_lastname'];
        }

        $this->addColumn('customer_id', array(
            'header' => Mage::helper('reports')->__('Customer'),
            'align' => 'right',
            'index' => 'customer_id',
            'type' => 'options',
            'options' => $options,
        ));
*/
         $this->addColumn('customer_id', array(
            'header' => Mage::helper('catalog')->__('Customer'),
            'index' => 'increment_id',
            //'filter' => false,
            //'sortable' => false,
            'renderer' => 'Ant_Advancereports_Block_Adminhtml_Customer_Renderer_Customer'
        ));

        $this->addColumn('increment_id', array(
            'header' => Mage::helper('reports')->__('Sales Order'),
            'align' => 'left',
            'index' => 'increment_id',
            'filter_index' => 'main_table.increment_id ',
            'type' => 'text'
        ));
        /*

        $status = array();
        foreach ($collections as $collection) {
            $status_string = split('_', $collection['status']);
            $status_name = '';
            foreach ($status_string as $string) {
                $status_name .= strtoupper(substr($string, 0, 1)) . substr($string, 1) . ' ';
            }
            $status_name = trim($status_name);
            if (strlen($status_name) == 0)
                $status_name = "Undefined";
            $status[$collection['status']] = $status_name;
        }

        $this->addColumn('status', array(
            'header' => Mage::helper('reports')->__('Order Status'),
            'align' => 'left',
            'index' => 'status',
            'filter_index' => 'main_table.status',
            'type' => 'options',
            'options' => $status,
        ));
*/
        $lateStatus['late'] = 'Late';
        $lateStatus['expired'] = 'Expired';
        $lateStatus[NULL] = '';

        $this->addColumn('latestatus', array(
            'header' => Mage::helper('reports')->__('Payment status'),
            'align' => 'left',
            'index' => 'latestatus',
            'filter_index' => 'a.latestatus',
            'type' => 'options',
            'options' => $lateStatus,
        ));
        
        $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('sales')->__('XML'));
        return parent::_prepareColumns();
    }

    public function getRowUrl($row) {
        return $row->getStatus();
    }

    public function _prepareMassaction() {
        $this->getMassactionBlock()->addItem('export', array(
            'label' => Mage::helper('catalog')->__('Export to CSV'),
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
        $this->getCollection()->setPageSize(0);
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
        $this->getCollection()->setPageSize(0);
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