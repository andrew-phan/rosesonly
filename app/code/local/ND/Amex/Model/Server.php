<?php
/**
 * ND Amex payment gateway
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so you can be sent a copy immediately.
 *
 * Original code copyright (c) 2008 Irubin Consulting Inc. DBA Varien
 *
 * @category ND
 * @package    ND_Amex
 * @copyright  Copyright (c) 2010 ND Amex
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ND_Amex_Model_Server extends Mage_Payment_Model_Method_Abstract
{
    protected $_code  = 'amex_server';

    protected $_isGateway               = false;
    protected $_canAuthorize            = false;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = false;
    protected $_canVoid                 = false;
    protected $_canUseInternal          = false;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = false;

    protected $_formBlockType = 'amex/server_form';
    protected $_paymentMethod = 'server';
    protected $_infoBlockType = 'amex/payment_info';

    protected $_order;
    
    protected $_paymentUrl = '';

    /**
     * Get order model
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if (!$this->_order) {
            $paymentInfo = $this->getInfoInstance();
            $this->_order = Mage::getModel('sales/order')
                            ->loadByIncrementId($paymentInfo->getOrder()->getRealOrderId());
        }
        return $this->_order;
    }
    
    /**
     * Grand total getter
     *
     * @return string
     */
    private function _getAmount()
    {
        /*if(Mage::getStoreConfig('payment/' . $this->getCode() . '/test_gateway'))
        {
            return 1000;
        }
        else
        {*/
            $_amount = (double)$this->getOrder()->getBaseGrandTotal();           
            return $_amount*100; 
        //}
    }

    /**
     * Get Customer Id
     *
     * @return string
     */
    public function getMerchantId()
    {
        $merchant_id = Mage::getStoreConfig('payment/' . $this->getCode() . '/merchant_id');            
        return $merchant_id;
    }

    public function getPassword()
    {
        $password = Mage::getStoreConfig('payment/' . $this->getCode() . '/password');        
        return $password;            
    }

    public function validate()
    {   
        return true;
    }

    public function getOrderPlaceRedirectUrl()
    {
        $url = Mage::getUrl('amex/' . $this->_paymentMethod . '/redirect');
        if(!$url) {
            $url = 'https://preprod-ae3g.its-online-services.com/Paypage/Create.aspx';
        }
        return $url;
    }

    /**
     * prepare params array to send it to gateway page via POST
     *
     * @return array
     */
    public function getFormFields()
    {        
        $fieldsArr = array();        
        $paymentInfo = $this->getInfoInstance();
        $lengs = 0;        
        
        /*$currency_code = Mage::app()->getStore()-> getCurrentCurrencyCode();
        $fields = array(
                    "SupplierID"=>$this->getMerchantId(),
                    "Amount"=>$this->_getAmount(), // 1000 - For Testing
                    "Password"=>$this->getPassword(),
                    "CountryCode"=>"USA",
                    "AmexOnly"=>"Y",
                    "CMName"=>"Y",
                    "Reference1"=>"N",
                    "Reference2"=>"N",
                    "BypassMPI"=>"N",
                    "CurrencyCode"=>$currency_code,
                    "Reference"=>$paymentInfo->getOrder()->getRealOrderId(),                                                    
                    "OnCompletionURL"=>Mage::getUrl('amex/' . $this->_paymentMethod . '/response', array('_secure' => true)),
                    "OnErrorURL"=>Mage::getUrl('amex/' . $this->_paymentMethod . '/response', array('_secure' => true))
                    );      
        
        foreach($fields as $key => $val)                
        {
            $fieldsArr[$key] = $val;
        }*/       
        
        $order_id = $paymentInfo->getOrder()->getRealOrderId();       
        $coutry_code = "GBR"; //Mage::getStoreConfig('general/country/default');
		$language_code = "GBR";
        $merchant_id = $this->getMerchantId();
        $merchant_pwd = $this->getPassword();
        $gateway_url=$this->getAmexServerUrl();
        $amount = $this->_getAmount();
        $currency_code =  "SAR"; //Mage::app()->getStore()->getCurrentCurrencyCode();
        $url =  $gateway_url."?SupplierID=".$merchant_id."&Password=".$merchant_pwd."&Reference=".$order_id."&Amount=".$amount."&CurrencyCode=".$currency_code."&CountryCode=".$coutry_code. "&Language=" . $language_code . "&AmexOnly=Y&CVV=Y&OrientationCode=ltr&BypassMPI=Y&AutoLevel1Settlement=Y&CMName=Y&Reference1=N&Reference2=N&OnCompletionURL=".Mage::getUrl('amex/' . $this->_paymentMethod . '/response', array('_secure' => true))."&OnErrorURL=".Mage::getUrl('amex/' . $this->_paymentMethod . '/response', array('_secure' => true)) ;
        //echo $url;die;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        $ch_result = curl_exec($ch);
        //$ch_error = curl_error($ch);
        //curl_getinfo($ch);
        curl_close($ch);
        /*$start = strpos($ch_result, "<PaypageURL>")+12;
        $end = strpos($ch_result, "</PaypageURL>") - $start;
        $this->_paymentUrl = htmlspecialchars_decode(substr($ch_result, $start, $end));*/
        
        //print_r($ch_result);die;
        $xmlString = simplexml_load_string($ch_result);
        if($xmlString->Code=='0')
            $this->_paymentUrl = $xmlString->PaypageURL;
        else {
            Mage::getSingleton('core/session')->addError(Mage::helper('core')->__('Some of the information you have provided is incorrect, please do try again. If the problem persists, please call American Express Customer Service Unit on toll free 800 124 2229 within the Kingdom of Saudi Arabia or +9661 474 9035 outside of the Kingdom. Thank you.'));
            
            /*mackove*/
            $redirect = $this->getUrl('checkout/cart');
            Header('Location: ' . $redirect);
            exit();
            
            $this->_redirect('checkout/cart');
            return;
        }
             
        
        return $fieldsArr;
    }

    /**
     * Get url of AMEX Shared Payment
     *
     * @return string
     */
    public function getAmexServerUrl()
    {
         if (!$url = Mage::getStoreConfig('payment/' . $this->getCode() . '/api_url')) {
             $url = 'https://preprod-ae3g.its-online-services.com/Paypage/Create.aspx';
         }
         return $url;
    }
    
    public function getAmexTransactionUrl()
    {
         if (!$this->_paymentUrl) {
             $this->_paymentUrl = 'https://preprod-ae3g.its-online-services.com/paypage/transaction.aspx';
         }
         return $this->_paymentUrl;
    }

    /**
     * Get debug flag
     *
     * @return string
     */
    public function getDebug()
    {
        return Mage::getStoreConfig('payment/' . $this->getCode() . '/debug_flag');
    }

    public function capture(Varien_Object $payment, $amount)
    {
        $payment->setStatus(self::STATUS_APPROVED)
            ->setLastTransId($this->getTransactionId());

        return $this;
    }

    public function cancel(Varien_Object $payment)
    {
        $payment->setStatus(self::STATUS_DECLINED)
            ->setLastTransId($this->getTransactionId());

        return $this;
    }

    /**
     * Return redirect block type
     *
     * @return string
     */
    public function getRedirectBlockType()
    {
        return $this->_redirectBlockType;
    }

    public function assignData($data)
    {
        //Mage::throwException(implode(',',$data));
        $result = parent::assignData($data);        
        /*if (is_array($data)) {
            $this->getInfoInstance()->setAdditionalInformation($key, isset($data[$key]) ? $data[$key] : null);
        }
        elseif ($data instanceof Varien_Object) {
            $this->getInfoInstance()->setAdditionalInformation($key, $data->getData($key));
        }*/
        return $result;
    }
    /**
     * Return payment method type string
     *
     * @return string
     */
    public function getPaymentMethodType()
    {
        return $this->_paymentMethod;
    }
    
    public function afterSuccessOrder($response)
    {
        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($response->Reference);
        $paymentInst = $order->getPayment()->getMethodInstance();
        //$paymentInst->setTransactionId($response['vpc_TransactionNo']); 
        $paymentInst->setStatus(self::STATUS_APPROVED)
                ->setLastTransId($response->Reference)
                ->setTransactionId($response->Reference);
                /*->setAdditionalInformation(ND_Amex_Model_Info::PAN_INFO,$response->PAN)
                ->setAdditionalInformation(ND_Amex_Model_Info::AUTH_CODE,$response->AuthCode)
                ->setAdditionalInformation(ND_Amex_Model_Info::SCHEME,$response->Scheme);*/
        
        $order->sendNewOrderEmail();                
        if ($order->canInvoice()) {
            $invoice = $order->prepareInvoice();
            
            $invoice->register()->capture();
            Mage::getModel('core/resource_transaction')
                ->addObject($invoice)
                ->addObject($invoice->getOrder())
                ->save();
        }
        $transaction = Mage::getModel('sales/order_payment_transaction');
        $transaction->setTxnId($response->Reference);
        $transaction->setOrderPaymentObject($order->getPayment())
                    ->setTxnType(Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE);
        $transaction->save();
        $order_status = Mage::helper('core')->__('Payment is successful.');
    
        $order->addStatusToHistory(Mage_Sales_Model_Order::STATE_PROCESSING, $order_status);
        $order->save();        
    }
    
}
