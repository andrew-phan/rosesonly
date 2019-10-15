<?php
/**
 * Created by PhpStorm.
 * User: dvhoang
 * Date: 2/25/14
 * Time: 4:40 PM
 */

class Ant_Advancereports_Block_Adminhtml_Order_Renderer_Occasion extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $order_id = $row->getData('increment_id');
        $order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
        $items = $order->getAllItems();


        foreach ($items as $item) {
            if($item->getProductType()== 'bundle'){
                $product = Mage::getModel('catalog/product')->load($item->getProductId());
                $occasion[] = nl2br($product->getResource()->getAttribute('occasions')->getFrontend()->getValue($product));
            }
        }

        if (count($occasion)) {
            $occasions = implode(', ', $occasion);
        }
        $occasions_text = $occasions;
        return $occasions_text;
    }
}