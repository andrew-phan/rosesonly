<?php

class Ant_Deliverymanagement_Block_Sales_Order_Create_Deliverydate extends Mage_Adminhtml_Block_Sales_Order_Create_Abstract
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_create_deliverydate');
    }

    public function getHeaderText()
    {
        return Mage::helper('sales')->__('Delivery date');
    }

    public function getHeaderCssClass()
    {
        return 'head-newsletter-list';
    }

    protected function _toHtml()
    {
        if (! Mage::getSingleton('adminhtml/quote')->getIsOldCustomer()) {
            return parent::_toHtml();
        }
        return '';
    }

}

