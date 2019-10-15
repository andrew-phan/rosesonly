<?php
class Ant_Adminhtml_Block_Customer_Address_Edit extends Mage_Customer_Block_Address_Edit 
{
       
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
    }
    
    public function getTitle()
    {
        if ($title = $this->getData('title')) {
            return $title;
        }
        if ($this->getAddress()->getId()) {
            $title = Mage::helper('customer')->__('Edit Friend\'s Address');
        }
        else {
            $title = Mage::helper('customer')->__('Add New Friend\'s Address');
        }
        return $title;
    }
    
    public function canSetAsDefaultBilling()
    {
        return false;
    }

    public function canSetAsDefaultShipping()
    {
        return false;
    }
}