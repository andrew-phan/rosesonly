<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Multishipping checkout controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
include_once("Mage/Checkout/controllers/MultishippingController.php");
class Ant_Checkout_MultishippingController extends Mage_Checkout_MultishippingController
{
    public function shippingPostAction()
    {
        $shippingMethods = $this->getRequest()->getPost('shipping_method');
        $dates = $this->getRequest()->getPost('dates');
        $times = $this->getRequest()->getPost('times');
        $times_today = $this->getRequest()->getPost('times_today');
        $notes = $this->getRequest()->getPost('notes');    
        session_start();
        $_SESSION['dates']= $dates;
	$_SESSION['notes']=  $notes;
        
        $deldate = new DateTime($dates[0]);
        $today = new DateTime();
        if($deldate->format('m/d/Y') == $today->format('m/d/Y'))
            $_SESSION['times']=  $times_today;
        else $_SESSION['times']=  $times;
        try {
            Mage::dispatchEvent(
                'checkout_controller_multishipping_shipping_post',
                array('request'=>$this->getRequest(), 'quote'=>$this->_getCheckout()->getQuote())
            );
            $this->_getCheckout()->setShippingMethods($shippingMethods);
            $this->_getCheckout()->setDeliveryDayTime( $dates, $times);
            $this->_getState()->setActiveStep(
                Mage_Checkout_Model_Type_Multishipping_State::STEP_BILLING
            );
            $this->_getState()->setCompleteStep(
                Mage_Checkout_Model_Type_Multishipping_State::STEP_SHIPPING
            );
            $this->_redirect('*/*/billing');
        }
        catch (Exception $e){
            $this->_getCheckoutSession()->addError($e->getMessage());
            $this->_redirect('*/*/shipping');
        }
    }

	public function testAction()
    {
        //$model = Mage::getModel('onestepcheckout/onestepcheckout');
		$model = Mage::getSingleton('checkout/type_multishipping');
            
		//$model->load('2');
		//$model->setData('sales_order_id', 29);
		//$model->setData('mw_deliverydate_date', '0/24/2012');
		//$model->setData('mw_deliverydate_time', '2:00 pm');
		//$model->save();
		
		echo get_class($model);
		//print_r ($model->getCollection()->getData());
        
		/*
		( [0] => Array ( [mw_onestepcheckout_date_id] => 1 
						[sales_order_id] => 28 
						[mw_customercomment_info] => 
						[mw_deliverydate_date] => 10/24/2012 
						[mw_deliverydate_time] => 2:00 pm 
						[status] => 0 
						[created_time] => 
						[update_time] => ) )                  
        */
    }
}
