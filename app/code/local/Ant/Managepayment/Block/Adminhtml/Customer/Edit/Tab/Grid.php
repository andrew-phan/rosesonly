<?php

class Ant_Managepayment_Block_Adminhtml_Customer_Edit_Tab_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId("Managepayment_Grid");
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
//        $this->setUseAjax(true);
    }

    protected function _prepareCollection() {
        $id = $this->getCustomerId();
        $orders = Mage::getModel("sales/order")
                ->getCollection()
                ->addFieldToFilter('customer_id', $id)
                ->addAttributeToSelect('entity_id');
        foreach ($orders as $order){
            $orderIds[] = $order->getId();
        }
//        $collection = Mage::getModel("sales/order_payment")
//                ->getCollection()
//                ->addFieldToFilter('entity_id', array('in' => $orderIds));
        
        $collection = Mage::getResourceModel('sales/order_grid_collection')
                ->addFieldToFilter('entity_id', array("in" => $orderIds));
        $collection->getSelect()->join(array('payment' => 'sales_flat_order_payment'),'payment.parent_id = main_table.entity_id',
                array('method'));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $payment_helper = Mage::helper('payment');
        $method_list = $payment_helper->getPaymentMethodList();
        $this->addColumn('real_order_id', array(
            'header' => Mage::helper('sales')->__('Order #'),
            'width' => '80px',
            'type' => 'text',
            'index' => 'increment_id',
        ));

        $this->addColumn('store_id', array(
            'header' => Mage::helper('sales')->__('Purchased From (Store)'),
            'width' => '120px',
            'index' => 'store_id',
            'type' => 'store',
            'store_view' => true,
            'display_deleted' => true,
        ));

        $this->addColumn('created_at', array(
            'header' => Mage::helper('sales')->__('Purchased On'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '200px',
        ));
        $this->addColumn('base_grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Base)'),
            'index' => 'base_grand_total',
            'type' => 'currency',
            'currency' => 'base_currency_code',
        ));

        $this->addColumn('grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Purchased)'),
            'index' => 'grand_total',
            'type' => 'currency',
            'currency' => 'order_currency_code',
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('sales')->__('Status'),
            'index' => 'status',
            'type' => 'options',
            'width' => '70px',
            'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
        ));
        $this->addColumn("method", array(
            "header" => __("Payment Method"),
            "align" => "left",
            "width" => "200px",
            "index" => "method",
            "type" => "options",
            "options" => $method_list
        ));
        return parent::_prepareColumns();
    }

    public function getRowUrl($row) {
//        return $this->getUrl("*/*/edit", array("id" => $row->getId()));
        return Mage::getModel('adminhtml/url')->getUrl('adminhtml/sales_order/view', array("order_id" => $row->getId()));
    }

    public function getCustomerId() {
        $id = $this->getRequest()->getParam("id");
        return $id;
    }

}