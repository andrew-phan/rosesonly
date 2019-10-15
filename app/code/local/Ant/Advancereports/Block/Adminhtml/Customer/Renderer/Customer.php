<?php

/**
 * Created by PhpStorm.
 * User: dvhoang
 * Date: 2/25/14
 * Time: 4:39 PM
 */
class Ant_Advancereports_Block_Adminhtml_Customer_Renderer_Customer extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $order_id = $row->getData('increment_id');
        $order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
        
        return $order->getCustomerFirstname().' '.$order->getCustomerLastname();
    }
}
