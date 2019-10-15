<?php
/**
 * Appmerce - Applications for Ecommerce
 * http://www.appmerce.com
 *
 * @extension   MasterCard Internet Gateway Service (MIGS) - Virtual Payment Client
 * @type        Payment method
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category    Magento Commerce
 * @package     Appmerce_Migs
 * @copyright   Copyright (c) 2011-2013 Appmerce (http://www.appmerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Appmerce_Migs_Block_Hosted_Placement extends Mage_Core_Block_Template
{
    public function __construct()
    {
    }

    /**
     * Return checkout session
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Return payment API model
     *
     * @return Appmerce_Migs_Model_Api
     */
    protected function getApi()
    {
        return Mage::getSingleton('migs/api_hosted');
    }

    /**
     * Return order instance by lastRealOrderId
     *
     * @return Mage_Sales_Model_Order
     */
    protected function _getOrder()
    {
        if ($this->getOrder()) {
            $order = $this->getOrder();
        }
        elseif ($this->getCheckout()->getLastRealOrderId()) {
            $order = Mage::getModel('sales/order')->loadByIncrementId($this->getCheckout()->getLastRealOrderId());
        }

        return $order;
    }

    /**
     * Return placement form fields
     *
     * @return array
     */
    public function getFormData()
    {
        return $this->getApi()->getFormFields($this->_getOrder());
    }

    /**
     * Return gateway path from admin settings
     *
     * @return string
     */
    public function getFormAction()
    {
        return $this->getApi()->getConfigData('vpc_url', $this->_getOrder()->getStoreId());
    }

}
