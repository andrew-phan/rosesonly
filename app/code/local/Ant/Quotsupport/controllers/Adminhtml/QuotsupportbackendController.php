<?php
class Ant_Quotsupport_Adminhtml_QuotsupportbackendController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
    {
		$this->loadLayout();
		//$this->_title($this->__("Backend Page Title"));
		
		$quoteId = $this->getRequest()->getParam('quote_id');
		$_quote = Mage::getModel('Quotation/Quotation')->load($quoteId);
		$customerId = $_quote->getCustomer()->getId();
		
		/*$_quote->commit();
		$products = $_quote->getItems();
		foreach($products as $p)
		{
			echo $p->getId().': '.$p->getPriceIncludingDiscount().'<br/>';
		}
		//echo $_quote->GetLinkedProduct()->getPrice();
		echo $_quote->GetConfigFormatedPriceWithTaxes();*/
		
		/*$model = new Ant_Quotsupport_Model_Ordercreate();
		$model->setOrderInfo($quoteId);
		if($model->create() != null)
			echo "Successful";
		else 
			echo "Fail";*/
		
		/*********************************************************************/
		/*require_once 'app/Mage.php';
		Mage::app();
		$quote = Mage::getModel('sales/quote')
			->setStoreId(Mage::app()->getStore('default')->getId());

		// for customer orders:
		$customer = Mage::getModel('customer/customer')->load($customerId);
		$quote->assignCustomer($customer);

		// add product(s)
		$product = Mage::getModel('catalog/product')->load($productId);
		$buyInfo = array('qty' => 1);
		/*$product->addOption(new Varien_Object(
				array(
					'product' => $product->getId(),
					'code' => 'option_ids',
					'value' => '5',
				)
		));*/
		/*$quote->addProduct($product, new Varien_Object($buyInfo));

		$_billingAddress = $customer->getDefaultBillingAddress();
		$addressData = array(
					'customer_address_id' => $_billingAddress->getId(),
					'prefix' => '',
					'firstname' => $_billingAddress->getFirstname(),
					'middlename' => '',
					'lastname' => $_billingAddress->getLastname(),
					'suffix' => '',
					'company' => '',
					'street' => array($_billingAddress->getStreet(),''),
					'city' => $_billingAddress->getCity(),
					'country_id' => $_billingAddress->getCountryId(),
					'region' => '',
					'region_id' => $_billingAddress->getRegionId(),
					'postcode' => $_billingAddress->getPostcode(),
					'telephone' => $_billingAddress->getTelephone(),
					'fax' => '',
				);

		$billingAddress = $quote->getBillingAddress()->addData($addressData);
		$shippingAddress = $quote->getShippingAddress()->addData($addressData);
		
		/*$address = Mage::getModel('Sales/Quote_Address');
		$address->importCustomerAddress($customer->getAddressesCollection()->getFirstItem());*/
		
		/*$quote->setBillingAddress($address);
		$quote->setShippingAddress($address);
		
		//$billingAddress = $quote->getBillingAddress()->addData($addressData);
		$shippingAddress = $quote->getShippingAddress();
		
		$shippingAddress->setCollectShippingRates(true)->collectShippingRates()
			->setShippingMethod('flatrate_flatrate')
			->setPaymentMethod('checkmo');

		$quote->getPayment()->importData(array('method' => 'checkmo'));

		$quote->collectTotals()->save();

		$service = Mage::getModel('sales/service_quote', $quote);
		$service->submitAll();
		$order = $service->getOrder();
		echo $order->getId();*/
			
			
		$this->renderLayout();
    }
	
}