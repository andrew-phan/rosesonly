<?php

class Ant_FEF_Block_Adminhtml_Sales_Order_View_Tab_Email extends Mage_Adminhtml_Block_Template implements Mage_Adminhtml_Block_Widget_Tab_Interface {

    protected $_chat = null;

    protected function _construct() {
       parent::__construct();
        $this->setId('fef');            
        $this->_parentTemplate = $this->getTemplate();   
        $this->setTemplate('fef/sales/order/view/tab/email.phtml'); 
    }

    public function getTabLabel() {
        return $this->__('Tab label');
    }

    public function getTabTitle() {
        return $this->__('Tab title');
    }

    public function canShowTab() {
        return true;
    }

    public function isHidden() {
        return false;
    }

    public function getOrder() {
        return Mage::registry('current_order');
    }

}