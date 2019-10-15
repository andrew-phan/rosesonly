<?php
/*
 *  Created on Aug 30, 2012
 *  Author Ivan Proskuryakov - volgodark@gmail.com - Magazento.com
 *  Copyright Proskuryakov Ivan. Magazento.com © 2011. All Rights Reserved.
 *  Single Use, Limited Licence and Single Use No Resale Licence ["Single Use"]
 */
?>
<?php

class Magazento_Pdfinvoice_Block_Admin_Abstract_Edit_Tab_Helporder extends Mage_Adminhtml_Block_Widget_Form {


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
{{pdf.order.entity_id}}</br>
{{pdf.order.state}}</br>
{{pdf.order.status}}</br>
{{pdf.order.coupon_code}}</br>
{{pdf.order.protect_code}}</br>
{{pdf.order.shipping_description}}</br>
{{pdf.order.is_virtual}}</br>
{{pdf.order.store_id}}</br>
{{pdf.order.customer_id}}</br>
{{pdf.order.base_discount_amount}}</br>
{{pdf.order.base_discount_canceled}}</br>
{{pdf.order.base_discount_invoiced}}</br>
{{pdf.order.base_discount_refunded}}</br>
{{pdf.order.base_grand_total}}</br>
{{pdf.order.base_shipping_amount}}</br>
{{pdf.order.base_shipping_canceled}}</br>
{{pdf.order.base_shipping_invoiced}}</br>
{{pdf.order.base_shipping_refunded}}</br>
{{pdf.order.base_shipping_tax_amount}}</br>
{{pdf.order.base_shipping_tax_refunded}}</br>
{{pdf.order.base_subtotal}}</br>
{{pdf.order.base_subtotal_canceled}}</br>
{{pdf.order.base_subtotal_invoiced}}</br>
{{pdf.order.base_subtotal_refunded}}</br>
{{pdf.order.base_tax_amount}}</br>
{{pdf.order.base_tax_canceled}}</br>
{{pdf.order.base_tax_invoiced}}</br>
{{pdf.order.base_tax_refunded}}</br>
{{pdf.order.base_to_global_rate}}</br>
{{pdf.order.base_to_order_rate}}</br>
{{pdf.order.base_total_canceled}}</br>
{{pdf.order.base_total_invoiced}}</br>
{{pdf.order.base_total_invoiced_cost}}</br>
{{pdf.order.base_total_offline_refunded}}</br>
{{pdf.order.base_total_online_refunded}}</br>
{{pdf.order.base_total_paid}}</br>
{{pdf.order.base_total_qty_ordered}}</br>
{{pdf.order.base_total_refunded}}</br>
{{pdf.order.discount_amount}}</br>
{{pdf.order.discount_canceled}}</br>
{{pdf.order.discount_invoiced}}</br>
{{pdf.order.discount_refunded}}</br>
{{pdf.order.grand_total}}</br>
{{pdf.order.shipping_amount}}</br>
{{pdf.order.shipping_canceled}}</br>
{{pdf.order.shipping_invoiced}}</br>
{{pdf.order.shipping_refunded}}</br>
{{pdf.order.shipping_tax_amount}}</br>
{{pdf.order.shipping_tax_refunded}}</br>
{{pdf.order.store_to_base_rate}}</br>
{{pdf.order.store_to_order_rate}}</br>
{{pdf.order.subtotal}}</br>
{{pdf.order.subtotal_canceled}}</br>
{{pdf.order.subtotal_invoiced}}</br>
{{pdf.order.subtotal_refunded}}</br>
{{pdf.order.tax_amount}}</br>
{{pdf.order.tax_canceled}}</br>
{{pdf.order.tax_invoiced}}</br>
{{pdf.order.tax_refunded}}</br>
{{pdf.order.total_canceled}}</br>
{{pdf.order.total_invoiced}}</br>
{{pdf.order.total_offline_refunded}}</br>
{{pdf.order.total_online_refunded}}</br>
{{pdf.order.total_paid}}</br>
{{pdf.order.total_qty_ordered}}</br>
{{pdf.order.total_refunded}}</br>
{{pdf.order.can_ship_partially}}</br>
{{pdf.order.can_ship_partially_item}}</br>
{{pdf.order.customer_is_guest}}</br>
{{pdf.order.customer_note_notify}}</br>
{{pdf.order.billing_address_id}}</br>
{{pdf.order.customer_group_id}}</br>
{{pdf.order.edit_increment}}</br>
{{pdf.order.email_sent}}</br>
{{pdf.order.forced_do_shipment_with_invoice}}</br>
{{pdf.order.gift_message_id}}</br>
{{pdf.order.payment_authorization_expiration}}</br>
{{pdf.order.paypal_ipn_customer_notified}}</br>
{{pdf.order.quote_address_id}}</br>
{{pdf.order.quote_id}}</br>
{{pdf.order.shipping_address_id}}</br>
{{pdf.order.adjustment_negative}}</br>
{{pdf.order.adjustment_positive}}</br>
{{pdf.order.base_adjustment_negative}}</br>
{{pdf.order.base_adjustment_positive}}</br>
{{pdf.order.base_shipping_discount_amount}}</br>
{{pdf.order.base_subtotal_incl_tax}}</br>
{{pdf.order.base_total_due}}</br>
{{pdf.order.payment_authorization_amount}}</br>
{{pdf.order.shipping_discount_amount}}</br>
{{pdf.order.subtotal_incl_tax}}</br>
{{pdf.order.total_due}}</br>
{{pdf.order.weight}}</br>
{{pdf.order.customer_dob}}</br>
{{pdf.order.increment_id}}</br>
{{pdf.order.applied_rule_ids}}</br>
{{pdf.order.base_currency_code}}</br>
{{pdf.order.customer_email}}</br>
{{pdf.order.customer_firstname}}</br>
{{pdf.order.customer_lastname}}</br>
{{pdf.order.customer_middlename}}</br>
{{pdf.order.customer_prefix}}</br>
{{pdf.order.customer_suffix}}</br>
{{pdf.order.customer_taxvat}}</br>
{{pdf.order.discount_description}}</br>
{{pdf.order.ext_customer_id}}</br>
{{pdf.order.ext_order_id}}</br>
{{pdf.order.global_currency_code}}</br>
{{pdf.order.hold_before_state}}</br>
{{pdf.order.hold_before_status}}</br>
{{pdf.order.order_currency_code}}</br>
{{pdf.order.original_increment_id}}</br>
{{pdf.order.relation_child_id}}</br>
{{pdf.order.relation_child_real_id}}</br>
{{pdf.order.relation_parent_id}}</br>
{{pdf.order.relation_parent_real_id}}</br>
{{pdf.order.remote_ip}}</br>
{{pdf.order.shipping_method}}</br>
{{pdf.order.store_currency_code}}</br>
{{pdf.order.store_name}}</br>
{{pdf.order.x_forwarded_for}}</br>
{{pdf.order.customer_note}}</br>
{{pdf.order.created_at}}</br>
{{pdf.order.updated_at}}</br>
{{pdf.order.total_item_count}}</br>
{{pdf.order.customer_gender}}</br>
{{pdf.order.base_custbalance_amount}}</br>
{{pdf.order.currency_base_id}}</br>
{{pdf.order.currency_code}}</br>
{{pdf.order.currency_rate}}</br>
{{pdf.order.custbalance_amount}}</br>
{{pdf.order.is_hold}}</br>
{{pdf.order.is_multi_payment}}</br>
{{pdf.order.real_order_id}}</br>
{{pdf.order.tax_percent}}</br>
{{pdf.order.tracking_numbers}}</br>
{{pdf.order.hidden_tax_amount}}</br>
{{pdf.order.base_hidden_tax_amount}}</br>
{{pdf.order.shipping_hidden_tax_amount}}</br>
{{pdf.order.base_shipping_hidden_tax_amount}}</br>
{{pdf.order.hidden_tax_invoiced}}</br>
{{pdf.order.base_hidden_tax_invoiced}}</br>
{{pdf.order.hidden_tax_refunded}}</br>
{{pdf.order.base_hidden_tax_refunded}}</br>

                    
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
