<?php
/*
 *  Created on AUG 30, 2012
 *  Author Ivan Proskuryakov  - Magazento.com
 *  Copyright Proskuryakov Ivan. Magazento.com © 2012. All Rights Reserved.
 *  Single Use, Limited Licence and Single Use No Resale Licence ["Single Use"]
 */
Class Magazento_Pdfinvoice_Model_Creditmemo extends Mage_Sales_Model_Order_Pdf_Abstract // AuIt_Pdf_Model_Pdf_Abstract
{

    protected $_streams;
    protected $_data;
    protected $_HTML;

    public function getPdf($invoices = array()) {
        $this->_pdf = new Zend_Pdf();
        $this->drawCredotmemos($invoices);
        foreach ($this->_streams as $stream )
        {
            $pdf = Zend_Pdf::parse($stream);
            foreach ($pdf->pages as $page) {
                $this->_pdf->pages[] = clone($page);
            }
        }  
        return $this->_pdf;    
    }
    
    protected function drawCredotmemos($invoices)
    {
        foreach ($invoices as $invoice) {
            
            $store = $invoice->getOrder()->getStore();
            Mage::register('current_invoice', $invoice);
            Mage::register('current_order', $invoice->getOrder());
            Mage::getModel('core/email_template')->emulateDesign($store->getId());
            
            $this->_invoice = $invoice;      
            $order = $invoice->getOrder();
            
            // Layout
            $package = Mage::getDesign();
            $layout = Mage::getModel('core/layout');
            /* @var $layout Mage_Core_Model_Layout */
            $layout->setArea($package->getArea());
            $update = $layout->getUpdate();
            $update->addHandle('STORE_'.Mage::app()->getStore()->getCode());
            $update->addHandle('THEME_'.$package->getArea().'_'.$package->getPackageName().'_'.$package->getTheme('layout'));

            $update->addHandle('sales_order_printinvoice');

            $layout->getUpdate()->load();
            $layout->generateXml();
            $layout->generateBlocks();
            $layout->setDirectOutput(false);
            
            $block = $layout->getBlock('sales.order.print.invoice');
            
            $this->_data = Mage::getModel('pdfinvoice/pdf')->processPDF($invoice,$order,$block,'creditmemo');
            $this->_streams[] = $this->_data;
        }
    }    
    
}
