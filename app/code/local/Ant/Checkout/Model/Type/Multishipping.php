<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Multishipping checkout model
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
include_once("Mage/Checkout/Model/Type/Multishipping.php");
class Ant_Checkout_Model_Type_Multishipping extends Mage_Checkout_Model_Type_Multishipping
{
    /**
     * Create orders per each quote address
     *
     * @return Mage_Checkout_Model_Type_Multishipping
     */
    public function createOrders()
    {
        $orderIds = array();
        $this->_validate();
        $shippingAddresses = $this->getQuote()->getAllShippingAddresses();
        $orders = array();

        if ($this->getQuote()->hasVirtualItems()) {
            $shippingAddresses[] = $this->getQuote()->getBillingAddress();
        }

        try {
            foreach ($shippingAddresses as $address) {
                $order = $this->_prepareOrder($address);
                session_start();
                $order->dates = $_SESSION['dates'][$address->getId()];
		$order->notes = $_SESSION['notes'][$address->getId()];
                $order->times = $_SESSION['times'][$address->getId()];
                $orders[] = $order;
                Mage::dispatchEvent(
                    'checkout_type_multishipping_create_orders_single',
                    array('order'=>$order, 'address'=>$address)
                );                
            }

            foreach ($orders as $order) {
                $order->place();
                $order->save();
                if ($order->getCanSendNewEmailFlag()){
                    $order->sendNewOrderEmail();
                }
                $model = Mage::getModel('onestepcheckout/onestepcheckout');               
                $model->setData('sales_order_id', $order->getId());
                $model->setData('mw_deliverydate_date',$order->dates);
                $model->setData('mw_deliverydate_time',$order->times);
				$model->setData('mw_customercomment_info',$order->notes);
                $model->save();
                $orderIds[$order->getId()] = $order->getIncrementId();
            }

            Mage::getSingleton('core/session')->setOrderIds($orderIds);
            Mage::getSingleton('checkout/session')->setLastQuoteId($this->getQuote()->getId());

            $this->getQuote()
                ->setIsActive(false)
                ->save();

            Mage::dispatchEvent('checkout_submit_all_after', array('orders' => $orders, 'quote' => $this->getQuote()));

            return $this;
        } catch (Exception $e) {
            Mage::dispatchEvent('checkout_multishipping_refund_all', array('orders' => $orders));
            throw $e;
        }
    }
}
