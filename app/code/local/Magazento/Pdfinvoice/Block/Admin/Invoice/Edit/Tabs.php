<?php
/*
 *  Created on Aug 30, 2012
 *  Author Ivan Proskuryakov - volgodark@gmail.com - Magazento.com
 *  Copyright Proskuryakov Ivan. Magazento.com © 2011. All Rights Reserved.
 *  Single Use, Limited Licence and Single Use No Resale Licence ["Single Use"]
 */
?>
<?php

class Magazento_Pdfinvoice_Block_Admin_Invoice_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $pdfType = Mage::helper('pdfinvoice')->getPdfTypeByController();
        $this->setId('pdfinvoice_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('pdfinvoice')->__('PDF '.$pdfType));
    }

    protected function _beforeToHtml() {
        $pdfType = Mage::helper('pdfinvoice')->getPdfTypeByController();
        $this->addTab('form_section_'.$pdfType, array(
            'label' => Mage::helper('pdfinvoice')->__('Content page'),
            'title' => Mage::helper('pdfinvoice')->__('Content page'),
            'content' => $this->getLayout()->createBlock('pdfinvoice/admin_abstract_edit_tab_form')->toHtml(),
        ));
        
        $this->addTab('form_section_first', array(
            'label' => Mage::helper('pdfinvoice')->__('Cover page'),
            'title' => Mage::helper('pdfinvoice')->__('Cover page'),
            'content' => $this->getLayout()->createBlock('pdfinvoice/admin_abstract_edit_tab_cover')->toHtml(),
        ));
        
        $this->addTab('form_section_last', array(
            'label' => Mage::helper('pdfinvoice')->__('Final page'),
            'title' => Mage::helper('pdfinvoice')->__('Final page'),
            'content' => $this->getLayout()->createBlock('pdfinvoice/admin_abstract_edit_tab_final')->toHtml(),
        ));
        
        $this->addTab('form_section_page', array(
            'label' => Mage::helper('pdfinvoice')->__('Page settings'),
            'label' => Mage::helper('pdfinvoice')->__('Page settings'),
            'content' => $this->getLayout()->createBlock('pdfinvoice/admin_abstract_edit_tab_page')->toHtml(),
        ));
        
        $this->addTab('form_section_helpgeneral', array(
            'label' => Mage::helper('pdfinvoice')->__('General variables'),
            'title' => Mage::helper('pdfinvoice')->__('General variables'),
            'content' => $this->getLayout()->createBlock('pdfinvoice/admin_abstract_edit_tab_helpgeneral')->toHtml(),
        ));        
        
        $this->addTab('form_section_helpinvoice', array(
            'label' => Mage::helper('pdfinvoice')->__('Invoice variables'),
            'title' => Mage::helper('pdfinvoice')->__('Invoice variables'),
            'content' => $this->getLayout()->createBlock('pdfinvoice/admin_abstract_edit_tab_helpinvoice')->toHtml(),
        ));
        
        $this->addTab('form_section_helporder', array(
            'label' => Mage::helper('pdfinvoice')->__('Order variables'),
            'title' => Mage::helper('pdfinvoice')->__('Order variables'),
            'content' => $this->getLayout()->createBlock('pdfinvoice/admin_abstract_edit_tab_helporder')->toHtml(),
        ));
        
        return parent::_beforeToHtml();
    }

}