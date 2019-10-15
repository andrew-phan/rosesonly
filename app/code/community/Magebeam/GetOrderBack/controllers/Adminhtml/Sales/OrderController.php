<?php
/**
 * Magebeam Get Order Back order controller
 *
 * @category    Magebeam
 * @package     Magebeam_GetOrderBack
 * @copyright   Copyright (c) 2012 Magebeam (http://www.magebeam.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Magebeam_GetOrderBack_Adminhtml_Sales_OrderController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Order id param name
     */
    const PARAM_ORDER_ID = 'order_id';

    /**
     * UnCancel order action
     */
    public function unCancelAction()
    {
        $orderId = $this->getRequest()->getParam(self::PARAM_ORDER_ID);
        $isOrderUnCanceled = Mage::helper('magebeam_getorderback')->unCancelOrder($orderId);
        if ($isOrderUnCanceled) {
            $this->_getSession()->addSuccess(
                $this->__('The order has been uncancelled.')
            );
        } else {
            $this->_getSession()->addError(
                Mage::helper('sales')->__('This order no longer exists.')
            );
        }
        $this->_redirect('*/sales_order/index');
    }
}