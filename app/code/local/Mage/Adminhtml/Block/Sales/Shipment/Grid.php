<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales orders grid
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sales_Shipment_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    /**
     * Initialization
     */
    public function __construct() {
        parent::__construct();
        $this->setId('sales_shipment_grid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
    }

    /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass() {
        return 'sales/order_shipment_grid_collection';
    }

    /**
     * Prepare and set collection of grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection() {
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        //var_dump($collection);
        $collection->getSelect()
                //->joinLeft(array('del'=>'ant_delivery'),'del.increment_id = main_table.increment_id',array('del.user_id'))
                ->joinLeft(array('mwo' => 'mw_onestepcheckout'), 
                        'mwo.sales_order_id = main_table.order_id', 
                        array(
                            'mwo.mw_deliverydate_date',
                            'mwo.mw_deliverydate_time', 
                            '(mwo.mw_deliverydate_date - CURDATE())*(mwo.mw_deliverydate_date - CURDATE()) as leftdate',
                            '(mwo.mw_deliverydate_date >= CURDATE()) as future', 
                            'mwo.print_do', 'mwo.print_msg'))
                ->joinLeft(array('d'=>'ant_delivery'), 'main_table.increment_id = d.increment_id','user_id')
                ->joinLeft(array('u'=>'admin_user'), 'd.user_id = u.user_id',array('CONCAT (firstname," ",lastname) as deliver', 'u.firstname'))
                ;
        
        $current = date("j F Y");

        //die($current);
        $collection                
                ->addAttributeToSort('print_do', 'ASC')
                ->addAttributeToSort('print_msg', 'ASC')
                ->addAttributeToSort('future', 'DESC')                
                ->addAttributeToSort('leftdate', 'ASC')
                ->addAttributeToSort('mw_deliverydate_time', 'DESC')
                ->addAttributeToSort('order_increment_id', 'DESC')
                ->addAttributeToSort('main_table.increment_id', 'DESC')
                ->addAttributeToSort('order_created_at', 'ASC')
                ->addAttributeToSort('shipping_name', 'ASC');
         
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare and add columns to grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns() {        
       
        $this->addColumn('mw_deliverydate_date', array(
            'header' => Mage::helper('sales')->__('Delivery Day'),
            'type' => 'date',
            'index' => 'mw_deliverydate_date',
            'filter_index' => 'mwo.mw_deliverydate_date' ,
        ));

        $this->addColumn('mw_deliverydate_time', array(
            'header' => Mage::helper('sales')->__('Delivery Time'),
            'type' => 'text',
            'index' => 'mw_deliverydate_time',
            'filter_index' => 'mwo.mw_deliverydate_time',
        ));

        $this->addColumn('order_increment_id', array(
            'header' => Mage::helper('sales')->__('Order #'),
            'index' => 'order_increment_id',
            'type' => 'text',
            'column_css_class' => 'sales'
        ));

        $this->addColumn('increment_id', array(
            'header' => Mage::helper('sales')->__('Shipment #'),
            'index' => 'increment_id',
            'type' => 'text',
        ));

        $this->addColumn('order_created_at', array(
            'header' => Mage::helper('sales')->__('Order Date'),
            'index' => 'order_created_at',
            'type' => 'datetime',
        ));

        $this->addColumn('shipping_name', array(
            'header' => Mage::helper('sales')->__('Ship to Name'),
            'index' => 'shipping_name',
        ));

         $this->addColumn("user", array(
            "header" => Mage::helper("sales")->__("Delivery by"),
            "align" => "left",
            "type" => "text",
            "index" => "firstname",
        ));

        $sets = array('0'=>'','1'=> 'printed');
        $this->addColumn('print_do', array(
            'header' => Mage::helper('sales')->__('Printed DO'),
            'index' => 'print_do',
            'type' => 'options',
            'options' => $sets
        ));

        $this->addColumn('print_msg', array(
            'header' => Mage::helper('sales')->__('Printed MSG'),
            'index' => 'print_msg',
            'type' => 'options',
            'options' => $sets
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('sales')->__('Action'),
            'width' => '50px',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('sales')->__('View'),
                    'url' => array('base' => '*/sales_shipment/view'),
                    'field' => 'shipment_id'
                )
            ),
            'filter' => false,
            'sortable' => false,
            'is_system' => true
        ));


        $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel XML'));

        return parent::_prepareColumns();
    }

    /**
     * Get url for row
     *
     * @param string $row
     * @return string
     */
    
    public function getRowUrl($row)  
    {  
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));  
    }   

    public function getRowClass($row) {
        $class = '';
        //$class="pointer";
        
        //if($row->getRowId()%2==0)
        //    $class .= " even";
        
        $today = date("Y-m-d"); 
        
        //if($today == $row->getMwDeliverydateDate() && ($row->getPrintDo()!=1 || $row->getPrintMsg()!=1))
        // Hoang : update logic to show redline
        if($row->getPrintDo()!=1 || $row->getPrintMsg()!=1)
            $class .= ' datenow';
        else 
            $class .= ' datepass';
        
        return $class;
    }
    
    /**
     * Prepare and set options for massaction
     *
     * @return Mage_Adminhtml_Block_Sales_Shipment_Grid
     */
    protected function _prepareMassaction() {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('shipment_ids');
        $this->getMassactionBlock()->setUseSelectAll(false);

        $this->getMassactionBlock()->addItem('pdfshipments_order', array(
            'label' => Mage::helper('sales')->__('PDF Packingslips'),
            'url' => $this->getUrl('*/sales_shipment/pdfshipments'),
        ));

        $this->getMassactionBlock()->addItem('print_shipping_label', array(
            'label' => Mage::helper('sales')->__('Print Shipping Labels'),
            'url' => $this->getUrl('*/sales_order_shipment/massPrintShippingLabel'),
        ));

        return $this;
    }

    /**
     * Get url of grid
     *
     * @return string
     */
    public function getGridUrl() {
        return $this->getUrl('*/*/*', array('_current' => true));
    }

}
