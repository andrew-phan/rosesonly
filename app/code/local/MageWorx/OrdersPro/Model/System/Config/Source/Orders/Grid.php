<?php
/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersPro
 * @copyright  Copyright (c) 2012 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * Orders Pro extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersPro
 * @author     MageWorx Dev Team
 */

class MageWorx_OrdersPro_Model_System_Config_Source_Orders_Grid
{   

    public function toOptionArray($isMultiselect=false) {
        $options = array(            
            array('value'=>'real_order_id', 'label'=> Mage::helper('sales')->__('Order #')),
            array('value'=>'store_id', 'label'=> Mage::helper('sales')->__('Purchased From (Store)')),
            array('value'=>'created_at', 'label'=> Mage::helper('sales')->__('Purchased On')),
            
            array('value'=>'product_names', 'label'=> Mage::helper('orderspro')->__('Product(s) Name(s)')),
            array('value'=>'product_skus', 'label'=> Mage::helper('orderspro')->__('SKU(s)')),
            
            array('value'=>'qnty', 'label'=> Mage::helper('orderspro')->__('Qnty')),
            array('value'=>'billing_name', 'label'=> Mage::helper('sales')->__('Bill to Name')),
            array('value'=>'shipping_name', 'label'=> Mage::helper('sales')->__('Ship to Name')),
            
            array('value'=>'billing_company', 'label'=> Mage::helper('orderspro')->__('Bill to Company')),
            array('value'=>'shipping_company', 'label'=> Mage::helper('orderspro')->__('Ship to Company')),
            
            array('value'=>'billing_street', 'label'=> Mage::helper('orderspro')->__('Bill to Street')),
            array('value'=>'shipping_street', 'label'=> Mage::helper('orderspro')->__('Ship to Street')),
            
            array('value'=>'billing_city', 'label'=> Mage::helper('orderspro')->__('Bill to City')),
            array('value'=>'shipping_city', 'label'=> Mage::helper('orderspro')->__('Ship to City')),
            
            array('value'=>'billing_region', 'label'=> Mage::helper('orderspro')->__('Bill to State')),
            array('value'=>'shipping_region', 'label'=> Mage::helper('orderspro')->__('Ship to State')),
            
            array('value'=>'billing_country', 'label'=> Mage::helper('orderspro')->__('Bill to Country')),
            array('value'=>'shipping_country', 'label'=> Mage::helper('orderspro')->__('Ship to Country')),
            
            array('value'=>'billing_postcode', 'label'=> Mage::helper('orderspro')->__('Billing Postcode')),
            array('value'=>'shipping_postcode', 'label'=> Mage::helper('orderspro')->__('Shipping Postcode')),
            
            array('value'=>'shipping_method', 'label'=> Mage::helper('orderspro')->__('Shipping Method')),
            array('value'=>'shipped', 'label'=> Mage::helper('orderspro')->__('Shipped')),
            array('value'=>'customer_email', 'label'=> Mage::helper('orderspro')->__('Customer Email')),
            array('value'=>'customer_group', 'label'=> Mage::helper('orderspro')->__('Customer Group')),
            array('value'=>'payment_method', 'label'=> Mage::helper('orderspro')->__('Payment Method')),
            
            array('value'=>'base_tax_amount', 'label'=> Mage::helper('orderspro')->__('Tax Amount (Base)')),
            array('value'=>'tax_amount', 'label'=> Mage::helper('orderspro')->__('Tax Amount (Purchased)')),
                        
            array('value'=>'coupon_code', 'label'=> Mage::helper('orderspro')->__('Coupon Code')),            
            array('value'=>'base_discount_amount', 'label'=> Mage::helper('orderspro')->__('Discount (Base)')),
            array('value'=>'discount_amount', 'label'=> Mage::helper('orderspro')->__('Discount (Purchased)')),
            
            array('value'=>'base_internal_credit', 'label'=> Mage::helper('orderspro')->__('Internal Credit (Base)')), // 30
            array('value'=>'internal_credit', 'label'=> Mage::helper('orderspro')->__('Internal Credit (Purchased)')), // 31
            
            array('value'=>'base_total_refunded', 'label'=> Mage::helper('orderspro')->__('Total Refunded (Base)')),
            array('value'=>'total_refunded', 'label'=> Mage::helper('orderspro')->__('Total Refunded (Purchased)')),
            
            array('value'=>'base_grand_total', 'label'=> Mage::helper('sales')->__('G.T. (Base)')),
            array('value'=>'grand_total', 'label'=> Mage::helper('sales')->__('G.T. (Purchased)')),
            
            array('value'=>'order_group', 'label'=> Mage::helper('orderspro')->__('Group')),
            array('value'=>'is_edited', 'label'=> Mage::helper('orderspro')->__('Edited')),
            
            array('value'=>'status', 'label'=> Mage::helper('sales')->__('Status')),
            array('value'=>'action', 'label'=> Mage::helper('sales')->__('Action'))
        );
            
        if (!Mage::getConfig()->getModuleConfig('MageWorx_CustomerCredit')->is('active', true)) {
            unset($options[30]); // Internal Credit (Base)
            unset($options[31]); // Internal Credit (Purchased)
        }
        
        //if (!$isMultiselect) array_pop($options);

        return $options;
    }
}