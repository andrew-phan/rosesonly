<?php
/**
 * Magebeam Get Order Back
 *
 * @category    Magebeam
 * @package     Magebeam_GetOrderBack
 * @copyright   Copyright (c) 2012 Magebeam (http://www.magebeam.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Magebeam_GetOrderBack_Model_UnCanceler extends Mage_Core_Model_Abstract
{
    /**
     * UnCancels order
     *
     * @param int $orderId Order id to unCancel
     *
     * @return bool True if order successfully unCanceled, false otherwise
     */
    public function unCancelOrder($orderId)
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = Mage::getModel('sales/order')->load($orderId);
        if (!$order->getId()) {
            return false;
        }
        $order->setState(
            Mage_Sales_Model_Order::STATE_HOLDED,
            Mage_Sales_Model_Order::STATE_HOLDED,
            Mage::helper('magebeam_getorderback')->__('The order has been uncancelled.')
        );
        $order->save();
        /** @var $item Mage_Sales_Model_Order_Item */
        foreach ($order->getAllItems() as $item) {
            $item->setQtyCanceled(0);
            $item->save();
        }
        $order = Mage::getModel('sales/order')->load($order->getId());
        return $order->getState() == Mage_Sales_Model_Order::STATE_HOLDED;
    }
}