<?php
class Sandipan_Unsubscribe_Block_Unsubscribeoverride extends Mage_Core_Block_Template
{
	public function getUnsubscribeFormActionUrl()
	{
		return $this->getUrl("unsubscribe/index/unsubscribecus");
	} 
}