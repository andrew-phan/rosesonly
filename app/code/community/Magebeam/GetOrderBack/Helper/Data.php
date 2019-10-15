<?php
/**
 * Magebeam Get Order Back data helper
 *
 * @category    Magebeam
 * @package     Magebeam_GetOrderBack
 * @copyright   Copyright (c) 2012 Magebeam (http://www.magebeam.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Magebeam_GetOrderBack_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Returns boolean flag: true if "UnCancel" action is allowed for order, false if not
     *
     * @return bool
     */
    public function isAllowedOrderUnCancelAction()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/uncancel');
    }

    /**
     * UnCancels order
     *
     * @param int $orderId Order id
     *
     * @return bool True if order has been unCanceled, false otherwise
     */
    public function unCancelOrder($orderId)
    {
        $unCanceler = Mage::getSingleton('magebeam_getorderback/unCanceler');
        return $unCanceler->unCancelOrder($orderId);
    }
}