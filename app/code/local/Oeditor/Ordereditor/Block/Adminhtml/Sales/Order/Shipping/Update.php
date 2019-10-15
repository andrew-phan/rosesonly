<?php
/**
 * Magento Order Editor Module
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the License Version.
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 *
 * @category   Order Editor
 * @package    Oeditor_Ordereditor
 * @copyright  Copyright (c) 2010 
 * @version    0.6.2
*/ 
class Oeditor_Ordereditor_Block_Adminhtml_Sales_Order_Shipping_Update extends Mage_Adminhtml_Block_Sales_Order_Create_Shipping_Method_Form
{ 
	public function getOrder()
	{
		$orderId = $this->getRequest()->getParam('order_id');
		$order = Mage::getModel('sales/order')->load($orderId);
		return $order;
	}
	
	public function getOrderStatus(){
		$order = $this->getOrder();
		$status = $order->getStatus();
		return $status;
	}
	
	public function getShippingRateCollection()
	{
 
	}
	
	public function getStores()
	{
		return Mage::getModel('storelocator/storeLocator')->getCollection()->addFieldToFilter('status',1)->setOrder('title','asc');
	}
	
	public function getShippingRates()
	{
		$shippingRates = $this->getShippingRateCollection();
		if(count($shippingRates)==0){
			Mage::getModel('editable/order_address')->recalculateShippingRates($this->getOrder());
			$shippingRates = $this->getShippingRateCollection();
		}		
		return $shippingRates;
	}
	
	public function getShippingAddressRates($params)
	{
 
	}
	
	public function getFormattedPrice($price)
	{
		return Mage::helper('core')->formatCurrency($price);
	}
	
}