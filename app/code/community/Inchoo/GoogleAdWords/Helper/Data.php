<?php
/**
 * INCHOO's FREE EXTENSION DISCLAIMER
 *
 * Please do not edit or add to this file if you wish to upgrade Magento
 * or this extension to newer versions in the future.
 *
 * Inchoo developers (Inchooer's) give their best to conform to
 * "non-obtrusive, best Magento practices" style of coding.
 * However, Inchoo does not guarantee functional accuracy of specific
 * extension behavior. Additionally we take no responsibility for any
 * possible issue(s) resulting from extension usage.
 *
 * We reserve the full right not to provide any kind of support for our free extensions.
 *
 * You are encouraged to report a bug, if you spot any,
 * via sending an email to bugreport@inchoo.net. However we do not guaranty
 * fix will be released in any reasonable time, if ever,
 * or that it will actually fix any issue resulting from it.
 *
 * Thank you for your understanding.
 */

/**
 * @category Inchoo
 * @package Inchoo_GoogleAdWords
 * @author Domagoj Potkoc <domagoj.potkoc@inchoo.net>
 * @copyright Inchoo <http://inchoo.net>
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Inchoo_GoogleAdWords_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getOrderTotal()
    {
        $orderId = (int) Mage::getSingleton('checkout/session')->getLastOrderId();

        $resurce = Mage::getModel('sales/order')->getResource();
        
        $select = $resurce->getReadConnection()->select()
                ->from(array('o' => $resurce->getTable('sales/order')), 'subtotal')
                ->where('o.entity_id=?', $orderId)
        ;

        $result = $resurce->getReadConnection()->fetchRow($select);

        if($result['subtotal'] > 0) {
            return round($result['subtotal'],2);
        }

        return 1;
    }

    public function isTrackingAllowed()
    {
        return Mage::getStoreConfigFlag('google/inchoo_google_adwords/enabled');
    }
}