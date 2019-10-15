<?php

class Ant_Advancereports_Block_Adminhtml_Order_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('advancereports/grid.phtml');
        $this->setId('orderGrid');
        $this->setDefaultSort('increment_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        //$this->setSubReportSize(false);
    }

    protected function _getCollectionClass()
    {
        return 'sales/order_collection';
    }


    /**
     * Set order
     *
     * @param string $attribute
     * @param string $dir
     * @return Mage_Reports_Model_Resource_Product_Collection
     */
    public function setOrder($attribute, $dir = self::SORT_ORDER_DESC)
    {
        if (in_array($attribute, array('carts', 'orders', 'ordered_qty'))) {
            $this->getSelect()->order($attribute . ' ' . $dir);
        } else {
            parent::setOrder($attribute, $dir);
        }

        return $this;
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass())
            ->addAttributeToSelect('created_at')
            ->addAttributeToSelect('increment_id')
            ->addAttributeToSelect('status')
            ->addAttributeToSelect('base_shipping_amount')
            ->addAttributeToSelect('base_shipping_tax_amount')
            ->addAttributeToSelect('base_subtotal_incl_tax')
            ->addAttributeToSelect('subtotal')
        ;

        $collection->getSelect()
            ->joinLeft(array('mwo' => 'mw_onestepcheckout'), 'mwo.sales_order_id = main_table.entity_id', array('mwo.mw_deliverydate_date', 'mwo.mw_deliverydate_time', 'mwo.mw_customercomment_info'))
            ->joinLeft(array('sfog' => 'sales_flat_order_grid'), 'sfog.entity_id = main_table.entity_id', array('sfog.shipping_name', 'sfog.billing_name', 'sfog.is_draft'))
            ->joinLeft(array('sfop' => 'sales_flat_order_payment'), 'sfop.parent_id = main_table.entity_id', array('sfop.method'))
            ->joinLeft(array('sfoa' => 'sales_flat_order_address'), "sfoa.parent_id=main_table.entity_id AND sfoa.address_type='shipping'", array('sfoa.postcode', 'sfoa.street'))
            ->joinLeft(array('sfig' => 'sales_flat_invoice_grid'), 'sfig.order_increment_id = main_table.increment_id', array('sfig.increment_id as invoice'))
            //->joinLeft(array('sfoi' => 'sales_flat_order_item'), "sfoi.order_id = main_table.entity_id AND product_type='bundle'", array('sfoi.product_id', 'sfoi.sku'))
            ->joinLeft(array('sfsg' => 'sales_flat_shipment_grid'), 'sfsg.order_increment_id = main_table.increment_id', array ('sfsg.entity_id'))
            ->joinLeft(array('sfst' => 'sales_flat_shipment_track'),'sfsg.entity_id = sfst.parent_id', array('sfst.track_number') )
    ;

        $collection
            // Hau Vo: if order is draft (is_draft == 1), don't show it
            ->addFieldToFilter('sfog.is_draft', array('eq' => 0));

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('created_at', array(
            'header' => 'Sales date',
            'index' => 'created_at',
            'type' => 'date',
            'filter_index' => 'main_table.created_at'
        ));

        $this->addColumn('invoice_no', array(
            'header' => 'Invoice No',
            'index' => 'invoice',
            'filter_index' => 'sfig.increment_id'
        ));

        $this->addColumn('increment_id', array(
            'header' => Mage::helper('reports')->__('RO Sales Order'),
            'index' => 'increment_id',
            'filter_index' => 'main_table.increment_id'
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('reports')->__('Status'),
            'index' => 'status',
            'filter_index' => 'main_table.status'
        ));

        $this->addColumn('billing_name', array(
            'header' => Mage::helper('reports')->__("Sender's Name"),
            'index' => 'billing_name',
            'filter_index' => 'sfog.billing_name'
        ));

        $this->addColumn('sku', array(
            'header' => Mage::helper('catalog')->__('Product'),
            'index' => 'increment_id',
            'renderer' => 'Ant_Advancereports_Block_Adminhtml_Order_Renderer_Sku'
        ));

        $this->addColumn('add_on', array(
            'header' => Mage::helper('catalog')->__('Add on'),
            'index' => 'increment_id',
            'sortable' => false,
            'renderer' => 'Ant_Advancereports_Block_Adminhtml_Order_Renderer_Addon'
        ));

        $this->addColumn('stalks', array(
            'header' => Mage::helper('catalog')->__('Stalks'),
            'index' => 'increment_id',
            'sortable' => false,
            'renderer' => 'Ant_Advancereports_Block_Adminhtml_Order_Renderer_Stalks'
        ));

        $this->addColumn('color', array(
            'header' => Mage::helper('catalog')->__('Color'),
            'index' => 'increment_id',
            'sortable' => false,
            'renderer' => 'Ant_Advancereports_Block_Adminhtml_Order_Renderer_Color'
        ));

        /*
        $this->addColumn('occasion', array(
            'header' => Mage::helper('core')->__('Occasion'),
            'index' => 'increment_id',
            'filter' => false,
            'sortable' => false,
            'renderer' => 'Ant_Advancereports_Block_Adminhtml_Order_Renderer_Occasion'
        ));
        */

        $this->addColumn('remark', array(
            'header' => Mage::helper('core')->__('Customer Remarks'),
            'index' => 'mw_customercomment_info',
            'filter_index' => 'mwo.mw_customercomment_info',
        ));

        $this->addColumn('mw_deliverydate_date', array(
            'header' => Mage::helper('core')->__('Del Date'),
            'type' => 'date',
            'index' => 'mw_deliverydate_date',
            'filter_index' => 'mwo.mw_deliverydate_date',
        ));

        $this->addColumn('del_zone', array(
            'header' => Mage::helper('core')->__('Del Zone'),
            'index' => 'mw_deliverydate_time',
            'filter_index' => 'mwo.mw_deliverydate_time',
        ));

        $this->addColumn('fef_so', array(
            'header' => Mage::helper('core')->__('FEF SO'),
            'index' => 'track_number',
            'filter_index' => 'sfst.track_number'
        ));


        $this->addColumn('price', array(
            'header' => Mage::helper('core')->__('Price'),
            'index' => 'subtotal',
            'type' => 'currency',
            'currency' => 'base_currency_code',
        ));

        $this->addColumn('price_gst', array(
            'header' => Mage::helper('core')->__('Price w/gst'),
            'index' => 'base_subtotal_incl_tax',
            'type' => 'currency',
            'currency' => 'base_currency_code',
        ));

        $this->addColumn('delivery', array(
            'header' => Mage::helper('core')->__('Delivery'),
            'index' => 'base_shipping_amount',
            'filter_index' => 'main_table.base_shipping_amount',
            'type' => 'currency',
            'currency' => 'base_currency_code',
        ));

        $this->addColumn('delvery_gst', array(
            'header' => Mage::helper('core')->__('Delivery w/gst'),
            'index' => 'base_shipping_tax_amount',
            'filter_index' => 'main_table.base_shipping_tax_amount',
            'type' => 'currency',
            'currency' => 'base_currency_code',
        ));

        $this->addColumn('grand_total', array(
            'header' => Mage::helper('core')->__('Grand Total'),
            'index' => 'increment_id',
            'filter' => false,
            'sortable' => false,
            'type' => 'currency',
            'currency' => 'base_currency_code',
            'renderer' => 'Ant_Advancereports_Block_Adminhtml_Order_Renderer_Total'
        ));

        $this->addColumn('payment', array(
            'header' => Mage::helper('core')->__('Payment'),
            'index' => 'method',
            'filter_index' => 'sfop.method'
        ));

        $this->addColumn('receiver', array(
            'header' => Mage::helper('core')->__('Receiver'),
            'index' => 'shipping_name',
            'filter_index' => 'sfog.shipping_name'
        ));

        $this->addColumn('address', array(
            'header' => Mage::helper('core')->__('Address'),
            'index' => 'street'
        ));

        $this->addColumn('postal_code', array(
            'header' => Mage::helper('core')->__('Postal Code'),
            'index' => 'postcode',
            'filter_index' => 'sfoa.postcode',
        ));

        $this->addColumn('_remarks', array(
            'header' => Mage::helper('core')->__('Remarks'),
        ));


        $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('sales')->__('XML'));

                return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $row->getStatus();
    }

    public function _prepareMassaction()
    {
        $this->getMassactionBlock()->addItem('export', array(
            'label' => Mage::helper('catalog')->__('Export to CSV'),
            'url' => $this->getUrl('*/*/massExport', array('_current' => true)),
        ));
    }

    /**
     * Retrieve Grid data as CSV
     * @param array $fields
     * @return string
     */
    public function getCsv($fields = array())
    {
        $time_start = microtime(true); 

        $this->_isExport = true;
        $this->_prepareGrid();

        //$csv = '';

        $this->getCollection()->getSelect()->limit();
        $this->getCollection()->setPageSize(0);
        $this->getCollection()->load();
        $this->_afterLoadCollection();

        $path = Mage::getBaseDir('var') . DS . 'export' ;
        $name =  'sales_order_report.'.date('YmdHis');

        $filename = $path . DS . $name . '.csv';
        //die($filename);
        $file = fopen($filename, 'w');
        $data = array();
        foreach ($this->_columns as $index => $column) {
            if (sizeof($fields) == 0 || (sizeof($fields) > 0 && in_array($index, $fields))) {
                if (!$column->getIsSystem()) {
                    $data[] = '"' . $column->getExportHeader() . '"';
                }
            }
        }

        //$csv .= implode(',', $data) . "\n";
        fwrite($file, implode(',', $data) . "\n");


        foreach ($this->getCollection() as $item) {
            $data = array();
            foreach ($this->_columns as $index => $column) {
                if (sizeof($fields) == 0 || (sizeof($fields) > 0 && in_array($index, $fields))) {
                    if (!$column->getIsSystem()) {
                        $data[] = '"' . str_replace(array('"', '\\'), array('""', '\\\\'),
                                $column->getRowFieldExport($item)) . '"';
                    }
                }
            }

            //$csv .= implode(',', $data) . "\n";
            fwrite($file, implode(',', $data) . "\n");
        }

        if ($this->getCountTotals()) {
            $data = array();
            foreach ($this->_columns as $column) {
                if (!$column->getIsSystem()) {
                    $data[] = '"' . str_replace(array('"', '\\'), array('""', '\\\\'),
                            $column->getRowFieldExport($this->getTotals())) . '"';
                }
            }
            //$csv .= implode(',', $data) . "\n";
            fwrite($file, implode(',', $data) . "\n");
        }

        //fclose($file);
      
        $time_end = microtime(true);
        $execution_time = ($time_end - $time_start)/60;
        $log_file = $path . DS . 'log.txt';
        $log =  fopen($log_file, 'w');
        fwrite($log, strval($execution_time));
        //fclose($log);

        header("Location: ". Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB)."var/export/" . $name . '.csv'); 
        /* Redirect browser */
        exit();
        //return $csv;
    }

    /**
     * @param array $fields
     * @return string
     */
    public function getXml($fields = array())
    {
        $this->_isExport = true;
        $this->_prepareGrid();
        $this->getCollection()->getSelect()->limit();
        $this->getCollection()->setPageSize(0);
        //$this->getCollection()->setPageSize(500)->setCurPage(1);
        $this->getCollection()->load();
        $this->_afterLoadCollection();
        $indexes = array();
        foreach ($this->_columns as $index => $column) {
            if (sizeof($fields) == 0 || (sizeof($fields) > 0 && in_array($index, $fields))) {
                if (!$column->getIsSystem()) {
                    $indexes[] = $column->getIndex();
                }
            }
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<items>';
        foreach ($this->getCollection() as $item) {
            $xml .= $item->toXml($indexes);
        }

        if ($this->getCountTotals()) {
            $xml .= $this->getTotals()->toXml($indexes);
        }
        $xml .= '</items>';
        return $xml;
    }

    function rutime($ru, $rus, $index) {
        return ($ru["ru_$index.tv_sec"]*1000 + intval($ru["ru_$index.tv_usec"]/1000))
     -  ($rus["ru_$index.tv_sec"]*1000 + intval($rus["ru_$index.tv_usec"]/1000));
    }


}