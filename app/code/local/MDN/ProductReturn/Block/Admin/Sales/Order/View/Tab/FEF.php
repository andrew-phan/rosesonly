<?php

class MDN_ProductReturn_Block_Admin_Sales_Order_View_Tab_FEF extends Mage_Adminhtml_Block_Template implements Mage_Adminhtml_Block_Widget_Tab_Interface {

    public function __construct() {
        parent::__construct();
        $this->setId('fef');
        $this->_parentTemplate = $this->getTemplate();
        $this->setTemplate('fef/sales/order/view/tab/email.phtml');
    }

    /**
     * ######################## TAB settings #################################
     */
    public function getTabLabel() {
        return Mage::helper('ProductReturn')->__('Email to FEF');
    }

    public function getTabTitle() {
        return Mage::helper('ProductReturn')->__('Email to FEF');
    }

    public function canShowTab() {
        return true;
    }

    public function isHidden() {
        return false;
    }

    public function getOrderId() {
        $order_id = $this->getRequest()->getParam('order_id');
        return $order_id;
    }

    public function getTemplateMail() {
        $enable = Mage::getStoreConfig('mageworx_sales/fef_settings/enable');

        $template = Mage::getStoreConfig('mageworx_sales/fef_settings/template');
        return $template;
    }

    public function getMailTo() {
        $mail_to = Mage::getStoreConfig('mageworx_sales/fef_settings/mail_to');
        return $mail_to;
    }

    public function getCc() {
        $cc = Mage::getStoreConfig('mageworx_sales/fef_settings/cc');
        return $cc;
    }
    
    public function getBcc() {
        $bcc = Mage::getStoreConfig('mageworx_sales/fef_settings/bcc');
        return $bcc;
    }
    
    public function getSendUrl()
    {
         return $this->getUrl('*/*/emailFEF', array(
                    'order_id' => $this->getOrderId()
         ));
    }
    
    public function getPreviewUrl()
    {
         return $this->getUrl('*/*/previewFEF', array(
                    'order_id' => $this->getOrderId()
         ));
    }
}
