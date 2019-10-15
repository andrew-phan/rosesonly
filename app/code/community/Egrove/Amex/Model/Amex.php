<?php
class Egrove_Amex_Model_Amex extends Mage_Payment_Model_Method_Abstract
{
	protected $_code = 'amex';
	protected $_formBlockType = 'amex/form_amex';
	protected $_infoBlockType = 'amex/info_amex';

	protected $_canSaveCc = true;

	public function __construct()
	{
	    parent::__construct();
	} 
	
	public function getOrderPlaceRedirectUrl()
	{
		if((int)$this->_getOrderAmount() > 0){
			return Mage::getUrl('amex/index/index', array('_secure' => true));
		}else{
			return false;
		}
	}
	private function _getOrderAmount()
	{
		$info = $this->getInfoInstance();
		if ($this->_isPlacedOrder()) {
			return (double)$info->getOrder()->getQuoteBaseGrandTotal();
		} else {
			return (double)$info->getQuote()->getBaseGrandTotal();
		}
	}
	private function _isPlacedOrder()
	{
		$info = $this->getInfoInstance();
		if ($info instanceof Mage_Sales_Model_Quote_Payment) {
			return false;
		} elseif ($info instanceof Mage_Sales_Model_Order_Payment) {
			return true;
		}
	}
	
	public function prepareSave()
	{
	     
	$info = $this->getInfoInstance();
	if ($this->_canSaveCc) {
	$info->setCcNumberEnc($info->encrypt($info->getCcCid().'&'.$info->getCcNumber()));
	}
	$info->setCcNumber(null)
	->setCcCid(null);
	return $this;
	} 
}
?>
