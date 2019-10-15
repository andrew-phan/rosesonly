<?php

/**
 * Created by PhpStorm.
 * User: dvhoang
 * Date: 2/25/14
 * Time: 4:39 PM
 */
class Ant_Advancereports_Block_Adminhtml_Order_Renderer_Addon extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $order_id = $row->getData('increment_id');
        $order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
        $items = $order->getAllItems();

        foreach ($items as $item) {
            if ($item->getParentItem()) {
                $product = Mage::getModel('catalog/product')->load($item->getProductId());
                if ($product->getAdditional() != 1) {
                    $addon[] = $item->getName();
                }
            }
        }
        if (count($addon)) {
            $addons = implode(', ', $addon);
        }
        $combine_text = $addons;
        return $combine_text;
    }
}
