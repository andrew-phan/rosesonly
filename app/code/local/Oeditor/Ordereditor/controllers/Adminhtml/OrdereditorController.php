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
 * @version    0.4.1
*/
require_once 'Zend/Json/Decoder.php';
class Oeditor_Ordereditor_Adminhtml_OrdereditorController extends Mage_Adminhtml_Controller_Action
{
	private $_order;
	public function recalcAction()
    {
    	echo $this->getLayout()->createBlock('ordereditor/adminhtml_sales_order_shipping_update')->setTemplate('ordereditor/tab/shipping-form.phtml')->toHtml();
    }
	
	public function newItemAction()
    {
    	echo $this->getLayout()->createBlock('ordereditor/adminhtml_sales_order_view_items_add')->setTemplate('ordereditor/items/add.phtml')->toHtml();
    }
    
	 public function getQtyAndDescAction()
    {
    	$sku = $this->getRequest()->getParam('sku');
    	$product = Mage::getModel('catalog/product')->getCollection()
    		->addAttributeToSelect('*')
    		->addAttributeToFilter('sku', $sku)
    		->getFirstItem();
    	$return = array();
    	$return['name'] = $product->getName();
    	
    	if($product->getSpecialPrice()) {
			$return['price'] = round($product->getSpecialPrice(), 2);
		} else {
			$return['price'] = round($product->getPrice(), 2);
		}

    	if($product->getManageStock()) {
    		$qty = $product->getQty();
    	} else {
    		$qty = 10;
    	}
    	$select = "<select class='new_item_qty'>";
    	$x = 1;
    	while($x <= $qty) {
    		$select .= "<option value='" . $x . "'>" . $x . "</option>";
    		$x++;
    	}
    	$select .= "</select>";
    	$return['select'] = $select;
    	echo Zend_Json::encode($return);
    }
	 
	public function editAction()
	{
		$order = $this->_initOrder();
		$orderArr = $order->getData();
		try {
			$preTotal = $order->getGrandTotal();
			$edits = array(); $rowTotal = 0;$rowDiscount = 0; $orderMsg = array(); $editFlag = 0; $manageInventory = 1; $productTax = 0;
			$r = $this->getRequest()->getParams();
			foreach($this->getRequest()->getParams() as $param) { 
			if($param = json_decode($param,Zend_Json::TYPE_ARRAY)) {
					$edits[] = $param;
				}
			}
			$msgs = array();$changes = array();
			//echo '<pre>';print_r($edits);die;
	 		foreach($edits as $edit) {
   
			if($edit['type']) {			
			if($edit['type'] == 'editItems' || $edit['type'] == 'newItems' || $edit['type'] == 'shippingmethod') {

			if($edit['type'] == "editItems"){
				$data = $edit;
			$comment = "";
		foreach($data['id'] as $key => $itemId) {
			$item = $order->getItemById($itemId);
			if($data['remove'][$key]) { 
				$order->removeItem($itemId);
				
			} else {

				$oldArray = array('price'=>$item->getPrice(), 'discount'=>$item->getDiscountAmount(), 'qty'=>$item->getQtyOrdered());

				$productTax = $productTax + $data['tax_amount'][$key];  // get the item tax
				//	$productTax = 0 ; // butt here setting tax 0 everything, so if the already added item price changes to higher/lower then the tax will set to zero,admin can set item new price inclusive tax manually
				
				$item->setTaxAmount($data['tax_amount'][$key]); // and also set item tax to zero so that, tax amount that is calculated(by customer-for old product while purchase), will not show in the item list
				$item->setTaxPercent($data['tax_percent'][$key]); // and also set item tax percentage to zero so that, tax amount that is calculated(by customer-for old product while purchase), will not show in the item list
				
				$item->setPrice($data['price'][$key]); 
				$item->setBasePrice($data['price'][$key]);
				$item->setBaseOriginalPrice($data['price'][$key]);
				$item->setOriginalPrice($data['price'][$key]);
				
				$item->setBaseRowTotal($data['price'][$key]);

				$item->setRowTotal($data['price'][$key] * $data['qty'][$key]); //new
					
				if($data['discount'][$key]) {
					$item->setDiscountAmount($data['discount'][$key]);
				}
				if($data['qty'][$key]) {
					$item->setQtyOrdered($data['qty'][$key]);
					

					
				}
				$item->save();
				
		
		
				 $rowTotal =  $rowTotal + $item->getRowTotal();
				$rowDiscount = $rowDiscount +  $item->getDiscountAmount() ;
				 
				$newArray = array('price'=>$item->getPrice(), 'discount'=>$item->getDiscountAmount(), 'qty'=>$item->getQtyOrdered());
				if($newArray['price'] != $oldArray['price'] || $newArray['discount'] != $oldArray['discount'] || $newArray['qty'] != $oldArray['qty']) {
					 
					if($newArray['price'] != $oldArray['price']) {
						 
					}
					if($newArray['discount'] != $oldArray['discount']) {
						 
					}
					if($newArray['qty'] != $oldArray['qty']) {
						 		
					}
				}

			}
		}
	}

if( $edit['type'] == 'shippingmethod')					
{
		$data = $edit;//echo '<pre>';print_r($data);die;
		$array = array();
		$orderStatus = $order->getStatus();
		
		$oldMethod = $order->getShippingDescription()." - $".substr($order->getShippingAmount(),0,-2);
		if($data['customcarrier'] != '' && $data['rateid'] == 'custom') {
 
			 $newMethod = strtolower($data['customcarrier']);
			 if($newMethod == 'other'){$newMethod = 'freeshipping';}
			$order->setShippingMethod($newMethod);
		
			$order->setShippingDescription($data['customcarrier']." - ".$data['customMethod']);
		} /*else {
	
			if($data['rateid'] != 'custom') {
				$shippingRate = Mage::getModel('editable/order_address_rate')->getCollection()->addFieldToFilter('rate_id',$data['rateid'])->getFirstItem();
				$order->setShippingMethod($shippingRate->getCode());
				$order->setShippingDescription($shippingRate->getCarrierTitle()." - ".$shippingRate->getMethodTitle());
			}
		}*/
		if($data['customPrice'] != '') {
	 
			$order->setShippingAmount($data['customPrice']);
			$order->setShippingInclTax($data['customPrice']);
			$order->setBaseShippingInclTax($data['customPrice']);
		} else {
 
			if($data['rateid'] != 'custom' && $data['rateid'] != "" ) {
				$order->setShippingAmount($shippingRate->getPrice());
			}
		}
		try{
		 
		 //echo '<pre>';print_r($order);die;
			$order->save();  //echo $order->getShippingAmount();die;
			$newMethod = $order->getShippingDescription()." - $".substr($order->getShippingAmount(),0,-2);
			$results = strcmp($oldMethod, $newMethod);
			if($results != 0) {


			}
			//return true;
		}catch(Exception $e){
			$array['status'] = 'error';
			$array['msg'] = "Error updating shipping method";
			return false;
		}

}

if( $edit['type'] == 'newItems')
{
		$data = $edit; 
		$comment = "";
		foreach($data['sku'] as $key => $sku) {
			$qty = $data['qty'][$key];
			
			$product = Mage::getModel('catalog/product')->getCollection()
				->addAttributeToFilter('sku', $sku)
				->addAttributeToSelect('*')
				->getFirstItem();
			
			
			
				//$this->editOrderItems($product,$order,$data,$rowTotal,$rowDiscount,$key,$qty);
				
						$quoteItem = Mage::getModel('sales/quote_item')->setProduct($product)
								->setQuote(Mage::getModel('sales/quote')->load($order->getQuoteId()));
							
							$orderItem = Mage::getModel('sales/convert_quote')->itemToOrderItem($quoteItem)->setProduct($product);
							$productPrice = $data['price'][$key];
							
							$orderItem->setPrice($productPrice);
							$orderItem->setBasePrice($productPrice);
							$orderItem->setBaseOriginalPrice($productPrice);
							$orderItem->setOriginalPrice($productPrice);
							$orderItem->setQtyOrdered($qty);
							
							
							$orderItem->setRowTotal($data['price'][$key] * $data['qty'][$key]); //new
								
							if($data['discount'][$key]) {
								$orderItem->setDiscountAmount($data['discount'][$key]);
							} else {
								$orderItem->setDiscountAmount(0);
							}
							$orderItem->setOrderId($order->getId());
							
							$orderItem->setOrder($order);
							$orderItem->save();
							
					
							//$rowTotal =  $rowTotal + $orderItem->getRowTotal() - $orderItem->getDiscountAmount() + $orderItem->getTaxAmount() + $orderItem->getWeeeTaxAppliedRowAmount();
							$rowTotal =  $rowTotal + $orderItem->getRowTotal();
							$rowDiscount = $rowDiscount +  $orderItem->getDiscountAmount() ;
							
							$order->addItem($orderItem);
							$order->save(); 
							
		}
	
}
 
					}
				}
			} 
 
			$order->setSubtotal($rowTotal);

			
			$order->setDiscountAmount('-'.$rowDiscount);
			
				$order->setBaseSubtotal($rowTotal);
				
				//$order->setBaseSubtotalInclTax($rowTotal+$productTax);
				//$order->setSubtotalInclTax($rowTotal+$productTax);
				
				$order->setBaseSubtotalInclTax($rowTotal);
				$order->setSubtotalInclTax($rowTotal);
				
				$order->setBaseGrandTotal($order->getShippingAmount()+$rowTotal+$productTax-$rowDiscount);
				
			/* set directly total order tax amount,so order exclusive grand total will automatically minus this tax from order inclusive grand total it is the amount tha will show the total tax summary (+)(shipping+product tax) */ $order->setTaxAmount($productTax); 
		  
			$order->setGrandTotal($order->getShippingAmount()+$rowTotal+$productTax-$rowDiscount);
			$order->save();	
		//	echo $order->getShippingAmount();die;
				$postTotal = $order->getGrandTotal();
			
			if(Mage::getStoreConfig('editorder/general/reauth')) {
				if($postTotal > $preTotal) {

					$payment = $order->getPayment();
					$orderMethod = $payment->getMethod();
					if($orderMethod != 'free' && $orderMethod != 'checkmo' && $orderMethod != 'purchaseorder') {
 
						$paymentDue = $postTotal - $preTotal ; 					
						//if(!$payment->authorize(1, $postTotal)) {
						if(!$payment->authorize(1, $paymentDue)) {
							echo "There was an error in re-authorizing payment.";
							return $this;
						}else{
						
							$additionalInformation  = $payment->getData('additional_information');
							//echo '<pre>';print_r($additionalInformation);die;
							$payment->save();
							$order->setTotalPaid($postTotal);
							$order->save();
						}
					}
				}
			}
			
			
			Mage::dispatchEvent('ordereditor_edit', array('order'=>$order));
			echo "Order updated successfully. The page will now refresh.";
			
			} catch(Exception $e) {
			echo $e->getMessage();
			//$this->_orderRollBack($order, $orderArr, $billingArr, $shippingArr);
		}
		return $this;
	}
	
	protected function editOrderItems($product,$order,$data,$rowTotal,$rowDiscount,$key,$qty)
	{
		/*--*/
					$quoteItem = Mage::getModel('sales/quote_item')->setProduct($product)
								->setQuote(Mage::getModel('sales/quote')->load($order->getQuoteId()));
							
							$orderItem = Mage::getModel('sales/convert_quote')->itemToOrderItem($quoteItem)->setProduct($product);
							$productPrice = $data['price'][$key];
							
							$orderItem->setPrice($productPrice);
							$orderItem->setBasePrice($productPrice);
							$orderItem->setBaseOriginalPrice($productPrice);
							$orderItem->setOriginalPrice($productPrice);
							$orderItem->setQtyOrdered($qty);
							
							
							$orderItem->setRowTotal($data['price'][$key] * $data['qty'][$key]); //new
								
							if($data['discount'][$key]) {
								$orderItem->setDiscountAmount($data['discount'][$key]);
							} else {
								$orderItem->setDiscountAmount(0);
							}
							$orderItem->setOrderId($order->getId());
							
							$orderItem->setOrder($order);
							$orderItem->save();
							
					
							//$rowTotal =  $rowTotal + $orderItem->getRowTotal() - $orderItem->getDiscountAmount() + $orderItem->getTaxAmount() + $orderItem->getWeeeTaxAppliedRowAmount();
							$rowTotal =  $rowTotal + $orderItem->getRowTotal();
							$rowDiscount = $rowDiscount +  $orderItem->getDiscountAmount() ;
							
							$order->addItem($orderItem);
							$order->save(); 
							
											/*--*/
	}
	protected function _orderRollBack($order, $orderArray, $billingArray, $shippingArray)
	{
		$order->setData($orderArray)->save();
		$order->getBillingAddress()->setData($billingArray)->save();
		$order->getShippingAddress()->setData($shippingArray)->save();
		$order->collectTotals()->save();
	}
	
	protected function _initOrder()
    {
        $id = $this->getRequest()->getParam('order_id');
        $order = Mage::getModel('ordereditor/order')->load($id);
//		$order = Mage::getModel('sales/order')->load($id);
	
        if (!$order->getId()) {
            $this->_getSession()->addError($this->__('This order no longer exists.'));
            $this->_redirect('*/*/');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        Mage::register('sales_order', $order);
        Mage::register('current_order', $order);
        return $order;
    }
    
	
	private function _loadOrder($orderId) {
		$this->_order = Mage::getModel('sales/order')->load($orderId);
		if(!$this->_order->getId()) return false;
		return true;
	}
	
	public function saveinvoicestatusAction() {
		$field = $this->getRequest()->getParam('field');
		$invoiceId = $this->getRequest()->getParam('invoice_id');
		$value = $this->getRequest()->getPost('value');
 
		if (!empty($field) && !empty($invoiceId)) {
			$invoice = Mage::getModel('sales/order_invoice')
                    ->load($invoiceId);
			$invoiceState = $invoice->setState($value);
			$invoice->save();

			$statuses = Mage::getModel('sales/order_invoice')->getStates();
			$invoiceState = $invoice->getState();
			if(isset($invoiceState))
			echo $invoiceStateLabel = $statuses[$invoiceState];
			else echo 'error in saving..';
		}
	}
	
	public function saveAction() {
		$field = $this->getRequest()->getParam('field');
		$type = $this->getRequest()->getParam('type');
		$orderId = $this->getRequest()->getParam('order');
		$value = $this->getRequest()->getPost('value');
		if (!empty($field) && !empty($type) && !empty($orderId)) {
			if(!empty($value)) {
				if(!$this->_loadOrder($orderId)) {
					$this->getResponse()->setBody($this->__('error: missing order number'));
				}
				$res = $this->_editAddress($type,$field,$value);
				if($res !== true) {
					$this->getResponse()->setBody($this->__('error: '.$res));
				} else {
						
						if($field == "order_status"){
							$statuses = Mage::getSingleton('sales/order_config')->getStatuses();
							foreach($statuses as $key=>$keyValue)
							{
								if($key == $value) { $this->getResponse()->setBody($keyValue);break;} 
							}
							
						}
						
						else{$this->getResponse()->setBody($value); }
				}
			} else {
				$this->getResponse()->setBody($this->__('error: value required'));
			}
		} else {
			$this->getResponse()->setBody('undefined error');
		}
	}
  

	private function _editAddress($type,$field,$value) {
  //echo $type.'='.$field.'='.$value;die;
		if($type == "bill") {
			  $address = $this->_order->getBillingAddress();
			 
			$addressSet = 'setBillingAddress';
		} elseif($type == "ship") {
			$address = $this->_order->getShippingAddress();
			$addressSet = 'setShippingAddress';
		} elseif($type == "cemail") {
				$this->_order->setCustomerEmail($value)->save();
				return true;
		} elseif($type == "cust_name") {

				$explodeName = explode(" ",$value);
				if(isset($explodeName[0]) && $explodeName[0] != ""){ $firstName = $explodeName[0]; $this->_order->setCustomerFirstname($firstName)->save();}
				if(isset($explodeName[1]) && $explodeName[1] != ""){ $lastName = $explodeName[1]; $this->_order->setCustomerLastname($lastName)->save();}
			
				
				return true;
		} elseif($type == "edit_ord") {
				$this->_order->setStatus($value)->save();
				return true;
		}
		 
		else {
			return 'type not defined';
		}

		$updated = false;
    	$fieldGet = 'get'.ucwords($field);
    	$fieldSet = 'set'.ucwords($field);


    	if($address->$fieldGet() != $value) {
 
    		if($field == 'country') {
    			$fieldSet = 'setCountryId';
    			$countries = array_flip(Mage::app()->getLocale()->getCountryTranslationList());
    			if(isset($countries[$value])) {
    				$value = $countries[$value];
    			} else {
    				return 'country not found';
    			}
    		}
    		if(substr($field,0,6) == 'street') {
    			$i = substr($field,6,1);
    			if(!is_numeric($i))
    				$i = 1;
    			$valueOrg = $value;
    			$value = array();
    			for($n=1;$n<=4;$n++) {
    				if($n != $i) {
	    				$value[] = $address->getStreet($n);
    				} else {
    					$value[] = $valueOrg;
    				}
    			}
    			$fieldSet = 'setStreet';
    		}
    		//update field and set as updated
    		$address->$fieldSet($value);
    		$updated = true;
    	}

		if($updated) {
			//			$this->_order->setStatus($value)->save();
			 if($field == "firstname") {
				$this->_order->setFirstName($value)->save();
				return true;
			}
			 if($field == "lastname") {
				$this->_order->setLastName($value)->save();
				return true;
			}

			 if($field == "street1") {
				$this->_order->setStreet1($value)->save();
				return true;
			}

			 if($field == "street2") {
				$this->_order->setStreet2($value)->save();
				return true;
			}

			 if($field == "street3") {
				$this->_order->setStreet3($value)->save();
				return true;
			}
			 if($field == "street4") {
				$this->_order->setStreet4($value)->save();
				return true;
			}

			 if($field == "city") {
				$this->_order->setCity($value)->save();
				return true;
			}
			 if($field == "region") {
				$this->_order->setRegion($value)->save();
				return true;
			}
			 if($field == "postcode") {
				$this->_order->setPostcode($value)->save();
				return true;
			}
			 if($field == "country") {
				$this->_order->setCountry($value)->save();
				return true;
			}
			 if($field == "telephone") {
				$this->_order->setTelephone($value)->save();
				return true;
			}
			 if($field == "fax") {
				$this->_order->setFax($value)->save();
				return true;
			}

			if($field == "company") {
				$this->_order->setCompany($value)->save();
				return true;
			}
			
			$this->_order->$addressSet($address);
        	$this->_order->save();
		}
		return true;
	}

        public function deliveryAction(){
           
            $id = $this->getRequest()->getParam('id');
            $date = new DateTime();
            $Deliverydate = $date->createFromFormat('d/m/Y', $this->getrequest()->getParam('_deliverydate'));
            $Deliverytime = $this->getRequest()->getParam('onestepcheckout_time');
            $back = $this->getRequest()->getParam('back');
                 
            $order = Mage::getModel('onestepcheckout/onestepcheckout');
            $order->load($id);
           
            if($order){               
                $order->setMwDeliverydateDate($Deliverydate->format('Y-m-d'));               
                $order->setMwDeliverydateTime($Deliverytime);                
                $order->save();               
            }
           
            $this->_redirectUrl($back);           
        }
}