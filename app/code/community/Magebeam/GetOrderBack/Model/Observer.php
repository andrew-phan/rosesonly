<?php
/**
 * Magebeam Get Order Back observer
 *
 * @category    Magebeam
 * @package     Magebeam_GetOrderBack
 * @copyright   Copyright (c) 2012 Magebeam (http://www.magebeam.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Magebeam_GetOrderBack_Model_Observer
{
    /**
     * Adds "UnCancel" button to order view page
     *
     * @param Varien_Event_Observer $event Event object
     *
     * @return Magebeam_GetOrderBack_Model_Observer
     */
    public function addUnCancelOrderButton(Varien_Event_Observer $event)
    {
        /** @var Mage_Adminhtml_Block_Sales_Order_View $block */
        $block = $event->getBlock();
        if ($block->getId() != 'sales_order_view') {
            return $this;
        }
        $this->_addUnCancelOrderButton($block);
        return $this;
    }

    /**
     * Adds "UnCancel" button to order view page
     *
     * @param Mage_Adminhtml_Block_Sales_Order_View $orderViewBlock Order view block
     *
     * @return Magebeam_GetOrderBack_Model_Observer
     *
     */
    protected function _addUnCancelOrderButton($orderViewBlock)
    {
        $order = $orderViewBlock->getOrder();
        if (!$order->getId() || $order->getState() != Mage_Sales_Model_Order::STATE_CANCELED) {
            return $this;
        }
        if (Mage::helper('magebeam_getorderback')->isAllowedOrderUnCancelAction()) {
            $orderViewBlock->addButton('uncancel', array(
                'label'     => Mage::helper('magebeam_getorderback')->__('UnCancel'),
                'class'     => 'go',
                'onclick'   => 'deleteConfirm(\''. Mage::helper('adminhtml')->__('Are you sure you want to do this?')
                    .'\', \'' . $orderViewBlock->getUrl('*/*/unCancel', array('order_id' => $order->getId())) . '\')',
            ));
        }
        return $this;
    }
}