<?php

//class Ant_Citibank_Model_PaymentLogic extends Mage_Payment_Model_Method_Cc {
class Ant_Citibank_Model_PaymentLogic extends Mage_Payment_Model_Method_Abstract {
    
  
    /**
     * unique internal payment method identifier
     */
    protected $_code = 'citibank';

    /**
     * this should probably be true if you're using this
     * method to take payments
     */
    protected $_isGateway = true;

    /**
     * can this method authorise?
     */
    protected $_canAuthorize = true;

    /**
     * can this method capture funds?
     */
    protected $_canCapture = true;

    /**
     * can we capture only partial amounts?
     */
    protected $_canCapturePartial = false;

    /**
     * can this method refund?
     */
    protected $_canRefund = false;

    /**
     * can this method void transactions?
     */
    protected $_canVoid = true;

    /**
     * can admins use this payment method?
     */
    protected $_canUseInternal = true;

    /**
     * show this method on the checkout page
     */
    protected $_canUseCheckout = true;

    /**
     * available for multi shipping checkouts?
     */
    protected $_canUseForMultishipping = true;

    /**
     * can this method save cc info for later use?
     */
    protected $_canSaveCc = false;

    protected $_redirectUrl= '';
    
    /**
     * this method is called if we are just authorising
     * a transaction
     */
    public function authorize(Varien_Object $payment, $amount) {
        
    }

    /**
     * this method is called if we are authorising AND
     * capturing a transaction
     */
    public function capture(Varien_Object $payment, $amount) {
        
    }

    /**
     * called if refunding
     */
    public function refund(Varien_Object $payment, $amount) {
        
    }

    /**
     * called if voiding a payment
     */
    public function void(Varien_Object $payment) {
        
    }

    public function getOrderPlaceRedirectUrl(){
        $this->_redirectUrl = Mage::getBaseUrl().'/citibank/pay';
        Mage::Log('returning redirect url:: ' . $this->_redirectUrl );   // not in log        
        return $this->_redirectUrl.'?vpc_Amount='.($this->_getAmount()*100).'&vpc_MerchTxnRef='.$this->_getOrderId();
    }
    
    private function _getAmount()
    {
        $info = $this->getInfoInstance();
        if ($this->_isPlaceOrder()) {
            return (double)$info->getOrder()->getGrandTotal();
        } else {
            return (double)$info->getQuote()->getGrandTotal();
        }
    }
    
    private function _isPlaceOrder()
    {
        $info = $this->getInfoInstance();
        if ($info instanceof Mage_Sales_Model_Quote_Payment) {
            return false;
        } elseif ($info instanceof Mage_Sales_Model_Order_Payment) {
            return true;
        }
    }
    
    private function _getOrderId()
    {
        $info = $this->getInfoInstance();

        if ($this->_isPlaceOrder()) {
            return $info->getOrder()->getIncrementId();
        } else {
            if (!$info->getQuote()->getReservedOrderId()) {
                $info->getQuote()->reserveOrderId();
            }
            return $info->getQuote()->getReservedOrderId();
        }
    }
}

?>