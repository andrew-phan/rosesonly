<?php
class Ant_Quotsupport_Model_Ordercreate extends Mage_Core_Model_Abstract
{
	private $_storeId = '1';
	private $_groupId = '1';
	private $_sendConfirmation = '0';
	private $orderData = array();
	private $_product;
	private $_sourceCustomer;
	private $_billingAddress;
	private $_shippingAddress;
	//private $_quote;
	//private $_sourceOrder;
	
	//public function setOrderInfo(Varien_Object $sourceOrder, Mage_Customer_Model_Customer $sourceCustomer, $quoteId)
	public function setOrderInfo($quoteId)
	{
		//$this->_sourceOrder = $sourceOrder;
		//$this->_sourceCustomer = $sourceCustomer;
		
		// Get Quotation
		$quote = Mage::getModel('Quotation/Quotation')->load($quoteId);
		//$this->_quote = $quote;
		
		// Get Customer info, StoreID
		$this->_sourceCustomer = $quote->getCustomer();
		$this->_storeId = $quote->getStoreId();
		
		//Get bundle product
		$quote->commit();
		$productId = $quote->GetLinkedProduct();
		$this->_product = Mage::getModel('catalog/product')->load($productId);
		
		$this->_billingAddress = $this->_sourceCustomer->getDefaultBillingAddress();
		$this->_shippingAddress = $this->_sourceCustomer->getDefaultShippingAddress();
		echo get_class($this->_billingAddress);
		
		//You can extract/refactor this if you have more than one product, etc.
		/*$this->_product = Mage::getModel('catalog/product')->getCollection()
			//->addAttributeToFilter('sku', 'Some value here...')
			//->addAttributeToSelect('*')
			->load($productId);*/
		
		//Load full product data to product object
		//$this->_product->load($this->_product->getId());
		//echo $this->_product->getId();
		
		//Load customer info
		$this->orderData = array(
				'session'       => array(
				'customer_id'   => $this->_sourceCustomer->getId(),
				'store_id'      => $this->_storeId,
			),
			'payment'       => array(
				'method'    => 'checkmo',
			),
			//'add_products'  =>array(
				//$this->_product->getId() => array('qty' => 1)
				$productId => array('qty' => 1),
			//),
			'order' => array(
				'currency' => 'USD',
				'account' => array(
					//'group_id' => $this->_groupId,
					'group_id' => $this->_sourceCustomer->getgroup_id(),
					'email' => $this->_sourceCustomer->getEmail()
				),
				'billing_address' => array(
					'customer_address_id' => $this->_billingAddress->getId(),
					'prefix' => '',
					'firstname' => $this->_billingAddress->getFirstname(),
					'middlename' => '',
					'lastname' => $this->_billingAddress->getLastname(),
					'suffix' => '',
					'company' => '',
					'street' => array($this->_billingAddress->getStreet(),''),
					'city' => $this->_billingAddress->getCity(),
					'country_id' => $this->_billingAddress->getCountryId(),
					'region' => '',
					'region_id' => $this->_billingAddress->getRegionId(),
					'postcode' => $this->_billingAddress->getPostcode(),
					'telephone' => $this->_billingAddress->getTelephone(),
					'fax' => '',
				),
				'shipping_address' => array(
					'customer_address_id' => $this->_shippingAddress->getId(),
					'prefix' => '',
					'firstname' => $this->_shippingAddress->getFirstname(),
					'middlename' => '',
					'lastname' => $this->_shippingAddress->getLastname(),
					'suffix' => '',
					'company' => '',
					'street' => array($this->_shippingAddress->getStreet(),''),
					'city' => $this->_shippingAddress->getCity(),
					'country_id' => $this->_shippingAddress->getCountryId(),
					'region' => '',
					'region_id' => $this->_shippingAddress->getRegionId(),
					'postcode' => $this->_shippingAddress->getPostcode(),
					'telephone' => $this->_shippingAddress->getTelephone(),
					'fax' => '',
				),
				'shipping_method' => 'flatrate_flatrate',
				'comment' => array(
					'customer_note' => 'This order has been programmatically created via import script.',
				),
				'send_confirmation' => $this->_sendConfirmation
			),
		);
	}
/**
* Retrieve order create model
*
* @return  Mage_Adminhtml_Model_Sales_Order_Create
*/
protected function _getOrderCreateModel()
{
	//return Mage::getSingleton('adminhtml/sales_order_create');
	return Mage::getSingleton('quotsupport/sales_order_create');
}
/**
* Retrieve session object
*
* @return Mage_Adminhtml_Model_Session_Quote
*/
protected function _getSession()
{
	return Mage::getSingleton('adminhtml/session_quote');
}
/**
* Initialize order creation session data
*
* @param array $data
* @return Mage_Adminhtml_Sales_Order_CreateController
*/
protected function _initSession($data)
{
	/* Get/identify customer */
	if (!empty($data['customer_id'])) 
	{
		$this->_getSession()->setCustomerId((int) $data['customer_id']);
	}
	/* Get/identify store */
	if (!empty($data['store_id'])) 
	{
		$this->_getSession()->setStoreId((int) $data['store_id']);
	}
	return $this;
}
/**
* Creates order
*/
public function create()
{
	$orderData = $this->orderData;
	if (!empty($orderData))
	{
		$this->_initSession($orderData['session']);
		try 
		{
			$this->_processQuote($orderData);
			if (!empty($orderData['payment'])) {
				$this->_getOrderCreateModel()->setPaymentData($orderData['payment']);
				$this->_getOrderCreateModel()->getQuote()->getPayment()->addData($orderData['payment']);
			}
			//$item = $this->_getOrderCreateModel()->getQuote()->getItemByProduct($this->_product);
			//if(!$item) echo 'null<br/>';
			//echo get_class($this->_getOrderCreateModel()->getQuote()).'<br/>';
			/*$option = new Mage_Sales_Model_Quote_Item_Option();
			$option->setProductId();
			$option->setItemId();
			$option->*/
			/*$item->addOption(new Varien_Object(
				array(
					'product' => $this->_product->getId(),
					'code' => 'option_ids',
					'value' => '5',
				)
			));
			/*$item->addOption(new Varien_Object(
				array(
					'product' => $this->_product,
					'code' => 'option_5',
					'value' => 'Some value here'
				)
			));*/
			Mage::app()->getStore()->setConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_ENABLED, "0");
			$_order = $this->_getOrderCreateModel()
				->importPostData($orderData['order'])
				->createOrder();
			$this->_getSession()->clear();
			Mage::unregister('rule_data');
			return $_order;
		}
		catch (Exception $e){
			Mage::log("Order save error...");
			var_dump($e);
		}
	}
	return null;
}

protected function _processQuote($data = array())
{
	/* Saving order data */
	if (!empty($data['order']))
	{
		$this->_getOrderCreateModel()->importPostData($data['order']);
	}
	$this->_getOrderCreateModel()->getBillingAddress();
	$this->_getOrderCreateModel()->setShippingAsBilling(true);
	/* Just like adding products from Magento admin grid */
	/*if(true)
	{
		$this->_quote->commit();
		$products = $this->_quote->getItems();
		foreach($products as $p)
		{
			echo $p->getId().': '.$p->getPriceIncludingDiscount().'<br/>';
			$this->_getOrderCreateModel()->addProduct( $p, array('qty' => 1));
		}
	}*/
	
	if (!empty($data['add_products']))
	{
		$this->_getOrderCreateModel()->addProducts($data['add_products']);
		//$this->_getOrderCreateModel()->addProduct($this->_product, array('qty' => 1));
	}
	
	/* Collect shipping rates */
	$this->_getOrderCreateModel()->collectShippingRates();
	/* Add payment data */
	if (!empty($data['payment']))
	{
		$this->_getOrderCreateModel()->getQuote()->getPayment()->addData($data['payment']);
	}
	$this->_getOrderCreateModel()
		->initRuleData()
		->saveQuote();
	if (!empty($data['payment']))
	{
		$this->_getOrderCreateModel()->getQuote()->getPayment()->addData($data['payment']);
	}
	return $this;
}
}