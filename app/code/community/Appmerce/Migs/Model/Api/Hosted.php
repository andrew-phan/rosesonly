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

class Appmerce_Migs_Model_Api_Hosted extends Appmerce_Migs_Model_Api
{
    protected $_code = 'migs_hosted';
    protected $_formBlockType = 'migs/form_hosted';
    protected $_infoBlockType = 'migs/info';

    // Magento features
    protected $_isGateway = false;
    protected $_canOrder = false;
    protected $_canAuthorize = true;
    protected $_canCapture = false;
    protected $_canCapturePartial = false;
    protected $_canRefund = true;
    protected $_canRefundInvoicePartial = true;
    protected $_canVoid = false;
    protected $_canUseInternal = false;
    protected $_canUseCheckout = true;
    protected $_canUseForMultishipping = true;
    protected $_isInitializeNeeded = true;
    protected $_canFetchTransactionInfo = false;
    protected $_canReviewPayment = false;
    protected $_canCreateBillingAgreement = false;
    protected $_canManageRecurringProfiles = false;

    // Special feature: we do NOT store Cc data in Magento
    protected $_canSaveCc = false;

    // Restrictions
    protected $_allowCurrencyCode = array();

    const API_CONTROLLER_PATH = 'migs/hosted/';

    // Local constants
    const EPS_SSL = 'ssl';
    const EPS_3D = 'threeDSecure';

    /**
     * Return available CC types
     */
    public function getCcTypes()
    {
        return array(
            'Amex' => Mage::helper('migs')->__('American Express Credit Card'),
            'AmexPurchaseCard' => Mage::helper('migs')->__('American Express Corporate Purchase Card'),
            'Bankcard' => Mage::helper('migs')->__('Bankcard Credit Card'),
            'Dinersclub' => Mage::helper('migs')->__('Diners Club Credit Card'),
            'GAPcard' => Mage::helper('migs')->__('GAP Inc, Card'),
            'JCB' => Mage::helper('migs')->__('JCB Credit Card'),
            'Loyalty' => Mage::helper('migs')->__('Loyalty Card'),
            'Mastercard' => Mage::helper('migs')->__('MasterCard Credit Card'),
            'Mondex' => Mage::helper('migs')->__('Mondex Card'),
            'PrivateLabelCard' => Mage::helper('migs')->__('Private Label Card'),
            'SafeDebit' => Mage::helper('migs')->__('SafeDebit Card'),
            'Solo' => Mage::helper('migs')->__('SOLO Credit Card'),
            'Style' => Mage::helper('migs')->__('Style Credit Card'),
            'Switch' => Mage::helper('migs')->__('Switch Credit Card'),
            'VisaDebit' => Mage::helper('migs')->__('Visa Debit Card'),
            'Visa' => Mage::helper('migs')->__('Visa Credit Card'),
            'VisaPurchaseCard' => Mage::helper('migs')->__('Visa Corporate Purchase Card'),
        );
    }

    /**
     * Get available Cc types
     *
     * @return array
     */
    public function getCcAvailableTypes()
    {
        $availableCcTypes = $this->getCcTypes();
        $selectedCcTypes = $this->getConfigData('cctypes');
        if (empty($selectedCcTypes)) {
            return array();
        }

        $selectedCcTypes = explode(',', $selectedCcTypes);
        foreach ($selectedCcTypes as $key => $value) {
            $ccTypes[$value] = $availableCcTypes[$value];
        }

        return $ccTypes;
    }

    /**
     * Generates array of fields for redirect form
     *
     * @return array
     */
    public function getFormFields($order)
    {
        $storeId = $order->getStoreId();
        $billingAddress = $order->getBillingAddress();
        $shippingAddress = $order->getShippingAddress();
        if (!$shippingAddress || !is_object($shippingAddress)) {
            $shippingAddress = $billingAddress;
        }

        $formFields = array();
        $formFields['vpc_Version'] = self::VPC_VERSION;
        $formFields['vpc_Command'] = self::COMMAND_PAY;
        $formFields['vpc_AccessCode'] = substr($this->getConfigData('access_code', $storeId), 0, 8);
        $formFields['vpc_MerchTxnRef'] = substr($order->getIncrementId(), 0, 40);
        $formFields['vpc_Merchant'] = substr($this->getConfigData('merchant_id', $storeId), 0, 16);
        $formFields['vpc_OrderInfo'] = substr($this->getConfig()->getOrderDescription($order), 0, 34);
        $formFields['vpc_Amount'] = $this->getGrandTotal($order);
        // These fields can cause E5009 errors (?)
        // $formFields['vpc_TxSource'] = self::TRANSACTION_SOURCE_INTERNET;
        // $formFields['vpc_TxSourceSubType'] = self::SOURCE_SUBTYPE_SINGLE;
        $formFields['vpc_Locale'] = substr(Mage::app()->getLocale()->getLocaleCode(), 0, 2);
        $formFields['vpc_ReturnURL'] = $this->_cutQueryFromUrl($this->getApiUrl('return', $storeId));
        if ($this->getConfigData('ticket_no', $storeId)) {
            $formFields['vpc_TicketNo'] = substr($this->getConfigData('ticket_no', $storeId), 0, 15);
        }

        // Address Verification Service
        if ($this->getConfigData('vpc_avs')) {
            $formFields['vpc_AVS_Street01'] = substr(str_replace("\n", ' ', $billingAddress->getStreet(-1)), 0, 128);
            $formFields['vpc_AVS_City'] = substr($billingAddress->getCity(), 0, 128);
            $formFields['vpc_AVS_StateProv'] = substr($billingAddress->getRegion(), 0, 128);
            if (strlen($billingAddress->getPostCode()) >= 4) {
                $formFields['vpc_AVS_PostCode'] = substr($billingAddress->getPostCode(), 0, 9);
            }
            $formFields['vpc_AVS_Country'] = Mage::getModel('directory/country')->load($billingAddress->getCountry())->getIso3Code();
        }

        // Expiration date mm/yy
        $month = $order->getPayment()->getCcExpMonth();
        $mm = (int)$month < 10 ? '0' . $month : $month;
        $yy = substr((int)$order->getPayment()->getCcExpYear(), 2, 2);

        // Collection-specific fields
        $collectMode = $this->getConfigData('collect_mode', $storeId);
        switch ($collectMode) {

            // No Card Details
            case 0 :
                break;

            // Only Card Type
            case 1 :
                $formFields['vpc_Card'] = $order->getPayment()->getCcType();
                break;

            // All Card Details
            case 2 :
                $formFields['vpc_Card'] = $order->getPayment()->getCcType();
                $formFields['vpc_CardNum'] = Mage::helper('core')->decrypt(Mage::getSingleton('core/session')->getCcNumberEnc());
                $formFields['vpc_CardExp'] = $yy . $mm;
                break;

            // All Card Details + Card Security Code (CSC)
            case 3 :
                $formFields['vpc_Card'] = $order->getPayment()->getCcType();
                $formFields['vpc_CardNum'] = Mage::helper('core')->decrypt(Mage::getSingleton('core/session')->getCcNumberEnc());
                $formFields['vpc_CardExp'] = $yy . $mm;
                $formFields['vpc_CardSecurityCode'] = Mage::helper('core')->decrypt(Mage::getSingleton('core/session')->getCcCidEnc());
                break;
        }

        // 3D Secure decision tree (if card AND if visa/mastercard AND if 3D enabled)
        if (isset($formFields['vpc_Card']) && in_array($formFields['vpc_Card'], array(
            'Visa',
            'Mastercard'
        )) && $this->getConfigData('use_3d', $storeId)) {
            $formFields['vpc_Gateway'] = self::EPS_3D;
        }
        elseif (isset($formFields['vpc_Card'])) {
            $formFields['vpc_Gateway'] = self::EPS_SSL;
        }

        // @see $_canSaveCc = false
        Mage::getSingleton('core/session')->setCcNumberEnc(null);
        Mage::getSingleton('core/session')->setCcCidEnc(null);
        Mage::getSingleton('core/session')->getCcOwner(null);
        Mage::getSingleton('core/session')->setCcLast4(null);
        Mage::getSingleton('core/session')->setCcNumber(null);
        Mage::getSingleton('core/session')->setCcCid(null);
        Mage::getSingleton('core/session')->setCcExpMonth(null);
        Mage::getSingleton('core/session')->setCcExpYear(null);
        Mage::getSingleton('core/session')->getCcSsIssue(null);
        Mage::getSingleton('core/session')->setCcSsStartYear(null);
        Mage::getSingleton('core/session')->setCcSsStartMonth(null);

        // vpc_SecureHash must come last to exclude itself
        $formFields['vpc_SecureHash'] = $this->getHash($formFields, $storeId);
        return $formFields;
    }

    /**
     * Get hash
     */
    public function getHash($formFields, $storeId = false)
    {
        ksort($formFields);
        $md5HashData = $this->getConfigData('secure_secret', $storeId);
        foreach ($formFields as $key => $value) {
            if (strlen($value) > 0) {
                $md5HashData .= $value;
            }
        }
        return strtoupper(md5($md5HashData));
    }

    /**
     * Return URLs
     */
    public function getApiUrl($key, $storeId = null)
    {
        return Mage::getUrl(self::API_CONTROLLER_PATH . $key, array(
            '_store' => $storeId,
            '_secure' => true
        ));
    }

}
