<?php
class Egrove_Amex_Block_Info_Amex extends Mage_Payment_Block_Info
{
	protected function _prepareSpecificInformation($transport = null)
	{
		if (null !== $this->_paymentSpecificInformation) {
			return $this->_paymentSpecificInformation;
		}
		$info = $this->getInfo();
		$transport = new Varien_Object();
		$transport = parent::_prepareSpecificInformation($transport);
		$transport->addData(array(
			Mage::helper('payment')->__('Credit Card No Last 4') => substr($info->getCcNumber(),-4,4),
			Mage::helper('payment')->__('Exp Date') => $info->getCcExpMonth() . ' / '.$info->getCcExpYear()
			
		));
		return $transport;
	}
}