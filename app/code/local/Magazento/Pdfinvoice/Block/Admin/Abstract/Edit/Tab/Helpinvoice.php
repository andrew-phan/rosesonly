<?php
/*
 *  Created on Aug 30, 2012
 *  Author Ivan Proskuryakov - volgodark@gmail.com - Magazento.com
 *  Copyright Proskuryakov Ivan. Magazento.com © 2011. All Rights Reserved.
 *  Single Use, Limited Licence and Single Use No Resale Licence ["Single Use"]
 */
?>
<?php

class Magazento_Pdfinvoice_Block_Admin_Abstract_Edit_Tab_Helpinvoice extends Mage_Adminhtml_Block_Widget_Form {


    protected function _prepareForm() {
        
        $form = new Varien_Data_Form(array('id' => 'edit_form_item', 'action' => $this->getData('action'), 'method' => 'post'));
        $form->setHtmlIdPrefix('item_');
        $fieldset = $form->addFieldset('base_fieldset', array('legend' => Mage::helper('pdfinvoice')->__('Additional Information'), 'class' => 'fieldset-wide'));

        if (Mage::helper('pdfinvoice')->versionUseWysiwig()) {
            $wysiwygConfig = Mage::getSingleton('pdfinvoice/wysiwyg_config')->getConfig();
        } else {
            $wysiwygConfig = '';
        }

        $fieldset->addField('Invoice', 'label', array(
                'label'=> Mage::helper('catalog')->__(''),
                'name'=>'',
                'note'  => Mage::helper('catalog')->__('
                    
{{pdf.invoice.entity_id}} <br/>
{{pdf.invoice.store_id}}  <br/>
{{pdf.invoice.base_grand_total}}  <br/>
{{pdf.invoice.shipping_tax_amount}}  <br/>
{{pdf.invoice.tax_amount}}   <br/>
{{pdf.invoice.base_tax_amount}}  <br/>
{{pdf.invoice.store_to_order_rate}}  <br/>
{{pdf.invoice.base_shipping_tax_amount}}   <br/>
{{pdf.invoice.base_discount_amount}}  <br/>
{{pdf.invoice.base_to_order_rate}}  <br/>
{{pdf.invoice.grand_total}}  <br/>
{{pdf.invoice.shipping_amount}}   <br/>
{{pdf.invoice.subtotal_incl_tax}}   <br/>
{{pdf.invoice.base_subtotal_incl_tax}}   <br/>
{{pdf.invoice.store_to_base_rate}}  <br/>
{{pdf.invoice.base_shipping_amount}}  <br/>
{{pdf.invoice.total_qty}}   <br/>
{{pdf.invoice.base_to_global_rate}} <br/>
{{pdf.invoice.subtotal}}  <br/>
{{pdf.invoice.base_subtotal}}  <br/>
{{pdf.invoice.discount_amount}} <br/>
{{pdf.invoice.billing_address_id}}  <br/>
{{pdf.invoice.is_used_for_refund}}   <br/>
{{pdf.invoice.order_id}} <br/>
{{pdf.invoice.email_sent}}<br/>
{{pdf.invoice.can_void_flag}}  <br/>
{{pdf.invoice.state}} <br/>
{{pdf.invoice.shipping_address_id}} <br/>
{{pdf.invoice.cybersource_token}} <br/>
{{pdf.invoice.store_currency_code}}  <br/>
{{pdf.invoice.transaction_id}}   <br/>
{{pdf.invoice.order_currency_code}}  <br/>
{{pdf.invoice.base_currency_code}} <br/>
{{pdf.invoice.global_currency_code}} <br/>
{{pdf.invoice.increment_id}}  <br/>
{{pdf.invoice.created_at}}  <br/>
{{pdf.invoice.updated_at}}  <br/>
{{pdf.invoice.customer_id}}  <br/>
{{pdf.invoice.invoice_status_id}}  <br/>
{{pdf.invoice.invoice_type}} <br/>
{{pdf.invoice.is_virtual}} <br/>
{{pdf.invoice.real_order_id}}    <br/>
{{pdf.invoice.total_due}}  <br/>
{{pdf.invoice.total_paid}}<br/>
{{pdf.invoice.hidden_tax_amount}}  <br/>
{{pdf.invoice.base_hidden_tax_amount}} <br/>
{{pdf.invoice.shipping_hidden_tax_amount}}  <br/>
{{pdf.invoice.base_shipping_hidden_tax_amount}} <br/>
{{pdf.invoice.shipping_incl_tax}} <br/>
{{pdf.invoice.base_shipping_incl_tax}} <br/>
{{pdf.invoice.base_total_refunded}} <br/>
                    
                '),            
        ));        
        
        

//        print_r($model->getData());
//        exit();
//        $form->setUseContainer(true);
//        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

}
