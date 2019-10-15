<?php
/**
 * Appmerce - Applications for Ecommerce
 * http://www.appmerce.com
 *
 * @extension   MasterCard Internet Gateway Service (MIGS) - Virtual Payment Client
 * @type        Payment method
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category    Magento Commerce
 * @package     Appmerce_Migs
 * @copyright   Copyright (c) 2011-2013 Appmerce (http://www.appmerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Appmerce_Migs_Model_Source_Cctypes
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'Amex',
                'label' => Mage::helper('migs')->__('American Express Credit Card')
            ),
            array(
                'value' => 'AmexPurchaseCard',
                'label' => Mage::helper('migs')->__('American Express Corporate Purchase Card')
            ),
            array(
                'value' => 'Bankcard',
                'label' => Mage::helper('migs')->__('Bankcard Credit Card')
            ),
            array(
                'value' => 'Dinersclub',
                'label' => Mage::helper('migs')->__('Diners Club Credit Card')
            ),
            array(
                'value' => 'GAPcard',
                'label' => Mage::helper('migs')->__('GAP Inc, Card')
            ),
            array(
                'value' => 'JCB',
                'label' => Mage::helper('migs')->__('JCB Credit Card')
            ),
            array(
                'value' => 'Loyalty',
                'label' => Mage::helper('migs')->__('Loyalty Card')
            ),
            array(
                'value' => 'Mastercard',
                'label' => Mage::helper('migs')->__('MasterCard Credit Card')
            ),
            array(
                'value' => 'Mondex',
                'label' => Mage::helper('migs')->__('Mondex Card')
            ),
            array(
                'value' => 'PrivateLabelCard',
                'label' => Mage::helper('migs')->__('Private Label Card')
            ),
            array(
                'value' => 'SafeDebit',
                'label' => Mage::helper('migs')->__('SafeDebit Card')
            ),
            array(
                'value' => 'Solo',
                'label' => Mage::helper('migs')->__('SOLO Credit Card')
            ),
            array(
                'value' => 'Style',
                'label' => Mage::helper('migs')->__('Style Credit Card')
            ),
            array(
                'value' => 'Switch',
                'label' => Mage::helper('migs')->__('Switch Credit Card')
            ),
            array(
                'value' => 'VisaDebit',
                'label' => Mage::helper('migs')->__('Visa Debit Card')
            ),
            array(
                'value' => 'Visa',
                'label' => Mage::helper('migs')->__('Visa Credit Card')
            ),
            array(
                'value' => 'VisaPurchaseCard',
                'label' => Mage::helper('migs')->__('Visa Corporate Purchase Card')
            ),
        );
    }

}
