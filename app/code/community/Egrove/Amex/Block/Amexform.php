<?php
class Egrove_Amex_Block_Amexform extends Mage_Core_Block_Template
{
    protected function cardexpformat($order)
	{
		$month=$order->getPayment()->getCcExpMonth();
		$year=substr($order->getPayment()->getCcExpYear(),2,2);
		if(strlen($month)==1)
		{
		$expyear=$year."0".$month;	
		}else
		{
		$expyear=$year.''.$month;
		}
		return $expyear;
	}
}