<?php

class Ant_Adminhtml_Block_Sales_Order_Create_Customer extends Mage_Adminhtml_Block_Sales_Order_Create_Customer
{
    public function getButtonsHtml()
    {
        $addButtonData = array(
            'label'     => Mage::helper('sales')->__('Create New Customer'),
            'onclick'   => "setLocation('".$this->getCreateCusUrl()."')",
            'class'     => 'add',
        );
        return $this->getLayout()->createBlock('adminhtml/widget_button')->setData($addButtonData)->toHtml();
    }
    
    public function getCreateCusUrl(){
        return $this->getUrl('adminhtml/customer/new');
    }
}