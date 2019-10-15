<?php
class Egrove_Amex_IndexController extends Mage_Core_Controller_Front_Action
{
	private $postData;
	private $secureHashSecret;
	private $hashInput;
	private $responseMap;

	
	private function debug($data)
	{
	    try{
	       Mage::log($data, null,'amex.log');
	    } catch (Exception $e) {
	        
	    }
	}

	public function indexAction()
	{
		$this->loadLayout();
		$this->renderLayout();	
	}

	public function successAction($request)
	{
		$orderIncrementId = $request['vpc_OrderInfo'];
		$order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
		
		$amexmodel = Mage::getModel('amex/amexdata');
	        $amount    = ($request['vpc_Amount'] / 100);
	        $amexdata  = array('amount' => $amount,'order_id' => $request['vpc_MerchTxnRef'],
	                    'authorized_id' => $request['vpc_AuthorizeId'],'message' => $request['vpc_Message'],
			    'transation_no' => $request['vpc_TransactionNo']);
	        $logid=$amexmodel->setData($amexdata)->save()->getId();
		
		$payment = $order->getPayment();
		$grandTotal = $order->getBaseGrandTotal();
			
		$payment->setTransactionId($request['vpc_ReceiptNo'])
		->setPreparedMessage("Payment Sucessfull Result:")
		->setIsTransactionClosed(0)
		->registerAuthorizationNotification($grandTotal);
		$order->save();
	  
	       //Capture Call
	       $vpcURL="https://vpos.amxvpos.com/vpcdps";
	       $data = array(
		'vpc_Version' => 1,
		'vpc_Command' => 'capture',
		'vpc_Merchant' => trim(Mage::getStoreConfig('payment/amex/mer_id')),
		'vpc_AccessCode' => trim(Mage::getStoreConfig('payment/amex/mer_access')),
		'vpc_MerchTxnRef' => $request['vpc_MerchTxnRef'],
		'vpc_Amount' => $request['vpc_Amount'],
		'vpc_TransNo' =>$request['vpc_TransactionNo'],
		'vpc_User' => trim(Mage::getStoreConfig('payment/amex/ama_user')),
		'vpc_Password' => trim(Mage::getStoreConfig('payment/amex/ama_pass'))
		);
		
		foreach($data as $key => $value) {
			if (strlen($value) > 0) {
				$this->addDigitalOrderField($key, $value);
			}
		}
		
		$this->sendMOTODigitalOrder($vpcURL, '');
		
		if (strlen($this->getErrorMessage()) == 0) {
 		$capturemessage = $this->getResultField("vpc_Message");
         	}
		
		$txnResponseCode = $this->getResultField("vpc_TxnResponseCode");
		$capturedAmount  = ($this->getResultField("vpc_CapturedAmount")) / 100;
		$transactionNr   = $this->getResultField("vpc_TransactionNo");
		$receiptNo       = $this->getResultField("vpc_ReceiptNo");
		
		if ($txnResponseCode != "7" && $txnResponseCode != "No Value Returned")
		{
		
		$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
		$order->setState('paid')->save();
		
		$payment->setTransactionId($receiptNo)
		->setIsTransactionClosed(1)
		->registerCaptureNotification($capturedAmount);
		$order->save();
			if ($invoice = $payment->getCreatedInvoice())
			{
			$message = Mage::helper('amex')->__('Notified customer about invoice #%s.', $invoice->getIncrementId());
			$comment = $order->sendNewOrderEmail()->addStatusHistoryComment($message)
			->setIsCustomerNotified(true)
			->save();
			}
			
			$amexmodel = Mage::getModel('amex/amexdata')->load($logid);
			$amexmodel->setCapture_rno($receiptNo);
			$amexmodel->setCapture_tno($transactionNr);
			$amexmodel->setCapture_message($capturemessage);
			$amexmodel->setCapture_amount($capturedAmount);
	                $amexmodel->save();
		}
	       
		$url = Mage::getUrl('checkout/onepage/success', array('_secure'=>true));
		$this->_redirectUrl($url);
		
	}

	protected function _getCheckout()
	{
		return Mage::getSingleton('checkout/session');
	}

	public function errorAction($response)
	{
		$amexmessage = $this->getResultDescription($response['vpc_TxnResponseCode']);
		$gotoSection = false;
		$session = $this->_getCheckout();
		if ($session->getLastRealOrderId())
		{
			$order = Mage::getModel('sales/order')->loadByIncrementId($session->getLastRealOrderId());
			if ($order->getId())
			{
				//Cancel order
				if ($order->getState() != Mage_Sales_Model_Order::STATE_CANCELED)
				{
					$order->registerCancellation($errorMsg)->save();
				}
				$quote = Mage::getModel('sales/quote')
				->load($order->getQuoteId());
				//Return quote
				if ($quote->getId())
				{
					$quote->setIsActive(1)
					->setReservedOrderId(NULL)
					->save();
					$session->replaceQuote($quote);
				}
				//Unset data
				$session->unsLastRealOrderId();
				//Redirect to payment step
				$gotoSection = 'payment';
				$this->_initLayoutMessages('core/session');
				$message  = $response['vpc_Message']."<br />";
				$message .= $amexmessage."<br />";
				$message .= "Please Try Again";
				Mage::getSingleton('core/session')->addError($message);
				$url = Mage::getUrl("checkout/onepage/index", array('_secure'=>true));
				Mage::register('redirect_url',$url);
				$this->_redirectUrl($url);
			}
		}
		return $gotoSection;
	}
	public function vpcreturnAction()
	{
		$response = $this->getRequest()->getParams();
		$this->secureHashSecret=trim(Mage::getStoreConfig('payment/amex/encry_key'));
        $this->debug('vpcreturnAction');
        $this->debug($response);
        //$threeDS=$response['vpc_3DSstatus'];
		if(($response['vpc_TxnResponseCode'] == "0") /*&& ($threeDS == "Y" || $threeDS == "A")*/) {
				$this->successAction($response);
		} else {
				$this->errorAction($response);
		}
	}

	private function getResultDescription($responseCode)
	{
		switch ($responseCode)
		{
			case "0" : $result = "Transaction Successful"; break;
			case "?" : $result = "Transaction status is unknown"; break;
			case "E" : $result = "Referred"; break;
			case "1" : $result = "Transaction Declined"; break;
			case "2" : $result = "Bank Declined Transaction"; break;
			case "3" : $result = "No Reply from Bank"; break;
			case "4" : $result = "Expired Card"; break;
			case "5" : $result = "Insufficient funds"; break;
			case "6" : $result = "Error Communicating with Bank"; break;
			case "7" : $result = "Payment Server detected an error"; break;
			case "8" : $result = "Transaction Type Not Supported"; break;
			case "9" : $result = "Bank declined transaction (Do not contact Bank)"; break;
			case "A" : $result = "Transaction Aborted"; break;
			case "C" : $result = "Transaction Cancelled"; break;
			case "D" : $result = "Deferred transaction has been received and is awaiting processing"; break;
			case "F" : $result = "3D Secure Authentication failed"; break;
			case "I" : $result = "Card Security Code verification failed"; break;
			case "L" : $result = "Shopping Transaction Locked (Please try the transaction again later)"; break;
			case "N" : $result = "Cardholder is not enrolled in Authentication scheme"; break;
			case "P" : $result = "Transaction has been received by the Payment Adaptor and is being processed"; break;
			case "R" : $result = "Transaction was not processed - Reached limit of retry attempts allowed"; break;
			case "S" : $result = "Duplicate SessionID (Amex Only)"; break;
			case "T" : $result = "Address Verification Failed"; break;
			case "U" : $result = "Card Security Code Failed"; break;
			case "V" : $result = "Address Verification and Card Security Code Failed"; break;
			default  : $result = "Unable to be determined"; 
		}
		return $result;
	}
	
	// Add VPC post data to the Digital Order
	private function addDigitalOrderField($field, $value) {
		
		if (strlen($value) == 0) return false;      
		if (strlen($field) == 0) return false;      
		
		$this->postData .= (($this->postData=="") ? "" : "&") . urlencode($field) . "=" . urlencode($value);
		
		$this->hashInput .= $field . "=" . $value . "&";
		
		return true;
		
	}
	
	// Obtain a one-way hash of the Digital Order data and
	// check this against what was received.
	public function hashAllFields() {
		$this->hashInput=rtrim($this->hashInput,"&");
		return strtoupper(hash_hmac('SHA256',$this->hashInput, pack("H*",$this->secureHashSecret)));
	}
	
	public function sendMOTODigitalOrder($vpcURL, $proxyHostAndPort = "", $proxyUserPwd = "") {
		$message = "";
		if (strlen($this->postData) == 0) return false;
		$this->debug($this->postData);
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
		$this->debug($response);
		
		ob_end_clean();
		$this->errorMessage = "";
		
		if(strchr($response,"<HTML>") || strchr($response,"<html>")) {;
		    $this->errorMessage = $response;
		} else {
		    if (curl_error($ch))
		          $this->errorMessage = "curl_errno=". curl_errno($ch) . " (" . curl_error($ch) . ")";
		}
		curl_close ($ch);
		
		$this->responseMap = array();
		
		if (strlen($message) == 0) {
		    $pairArray = explode("&", $response);
		    foreach ($pairArray as $pair) {
		        $param = explode("=", $pair);
		        $this->responseMap[urldecode($param[0])] = urldecode($param[1]);
		    }
		    return true;
		} else {
				return false;
		}

	}
	public function getErrorMessage() {
		return $this->errorMessage;
	}
	private function null2unknown($key) {

		if (array_key_exists($key, $this->responseMap)) {
		    if (!is_null($this->responseMap[$key])) {
		        return $this->responseMap[$key];
		    }
		} 
		return "No Value Returned";
	}

	public function getResultField($field) {
		return $this->null2unknown($field);
	}
	
}