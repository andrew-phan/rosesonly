<?php
class Egrove_Amex_TestController extends Mage_Core_Controller_Front_Action
{
    private $postData;
    private $secureHashSecret;
    private $hashInput;
    private $responseMap;
    
    public function testAction($request)
    {
        $orderIncrementId = 'SO00003447';
        $vpc_Amount = 1400;
        $vpc_TransactionNo = 205;
        $vpc_MerchTxnRef = 'SO00003447';
        
        //Capture Call
        $vpcURL="https://vpos.amxvpos.com/vpcdps";
        $data = array(
            'vpc_Version'   => 1,
            'vpc_Command'   => 'capture',
            'vpc_Merchant'  => trim(Mage::getStoreConfig('payment/amex/mer_id')),
            'vpc_AccessCode' => trim(Mage::getStoreConfig('payment/amex/mer_access')),
            'vpc_MerchTxnRef' => $vpc_MerchTxnRef,
            'vpc_Amount'    => $vpc_Amount,
            'vpc_TransNo'   =>$vpc_TransactionNo,
            //'vpc_User'      => 'resesama',//trim(Mage::getStoreConfig('payment/amex/ama_user')),
            //'vpc_Password'  => 'password0' //trim(Mage::getStoreConfig('payment/amex/ama_pass'))
            'vpc_User'      => trim(Mage::getStoreConfig('payment/amex/ama_user')),
            'vpc_Password'  => trim(Mage::getStoreConfig('payment/amex/ama_pass'))
        );
        
        foreach($data as $key => $value) {
            if (strlen($value) > 0) {
                $this->addDigitalOrderField($key, $value);
            }
        }
        
        print_r($this->postData);
        echo "<br/><br/><br/>";
        $response = $this->sendMOTODigitalOrder($vpcURL, '');
        print_r($response);
    }

    public function sendMOTODigitalOrder($vpcURL, $proxyHostAndPort = "", $proxyUserPwd = "") {
        $message = "";
        ob_start();
        $ch = curl_init();
        curl_setopt ($ch, CURLOPT_URL, $vpcURL);
        curl_setopt ($ch, CURLOPT_POST, 1);
        curl_setopt ($ch, CURLOPT_POSTFIELDS, $this->postData);
        
        if (strlen($proxyHostAndPort) > 0) {
            if (strlen($proxyUserPwd) > 0) {
                curl_setopt ($ch, CURLOPT_PROXY, $proxyHostAndPort, CURLOPT_PROXYUSERPWD, $proxyUserPwd);
            }
            else {
                curl_setopt ($ch, CURLOPT_PROXY, $proxyHostAndPort);
            }
        }
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_exec ($ch);
        $response = ob_get_contents();
        ob_end_clean();
        return $response;
    }
    
    private function addDigitalOrderField($field, $value)
    {
        if (strlen($value) == 0) return false;
        if (strlen($field) == 0) return false;
        $this->postData .= (($this->postData=="") ? "" : "&") . urlencode($field) . "=" . urlencode($value);
        $this->hashInput .= $field . "=" . $value . "&";
        return true;
    }
}
