<?php

class Mage_Adminhtml_Block_Sales_Order_Grid_Render_Email extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $order)
    {
        $data = $order->getData($this->getColumn()->getIndex());
        $email  = $order->getData('email_sent') ? 'Order' : '';
        $email .= $order->getData('invoice_email_cnt') ? ($email?', ':'').'Invoice' : '';
        $email .= $order->getData('shipment_email_cnt') ? ($email?', ':'').'Shipment' : '';
        if ($email){
            $data .= '<br/>[<i>'.$email.'</i>]';
        }
        return $data;
    }

}
