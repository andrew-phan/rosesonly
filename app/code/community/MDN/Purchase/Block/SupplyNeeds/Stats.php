<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright  Copyright (c) 2009 Maison du Logiciel (http://www.maisondulogiciel.com)
 * @author : Olivier ZIMMERMANN
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_Purchase_Block_SupplyNeeds_Stats extends Mage_Adminhtml_Block_Widget_Form {

    private $_suppliers = null;

    /**
     * Return suppliers
     */
    public function getSuppliers() {
        if ($this->_suppliers == null) {
            $supplierIds = mage::getResourceModel('Purchase/SupplyNeeds_collection')->getSupplierIds();
            $this->_suppliers = mage::getModel('Purchase/Supplier')
                            ->getCollection()
                            ->addFieldToFilter('sup_id', array('in' => $supplierIds));
        }
        return $this->_suppliers;
    }

    /**
     * Return amount for one supplier / one status
     */
    public function getAmount($supplier, $supplyNeedStatus) {
        return mage::getResourceModel('Purchase/SupplyNeeds_collection')->getAmount($supplier->getId(), $supplyNeedStatus);
    }

    /**
     * Return supply needs statuses
     */
    public function getStatuses() {
        $statuses = array();
        $statuses['qty_min'] = Mage::helper('purchase')->__('Min qty');
        $statuses['qty_max'] = Mage::helper('purchase')->__('Max qty');
        return $statuses;
    }

    public function checkFreeCarriage($supplier, $total) {
        $result = false;
        $minAmount = $supplier->getsup_free_carriage_amount();
        if (($minAmount == '') || ($minAmount == 0))
            $result = true;
        else {
            if ($total >= $minAmount)
                $result = true;
        }

        if ($result)
            return '<font color="green">OK (' . $minAmount . ')</font>';
        else
            return '<font color="red">NOK (' . $minAmount . ')</font>';
    }

    public function checkMinOrder($supplier, $total) {
        $result = false;
        $minAmount = $supplier->getsup_order_mini();
        if (($minAmount == '') || ($minAmount == 0))
            $result = true;
        else {
            if ($total >= $minAmount)
                $result = true;
        }

        if ($result)
            return '<font color="green">OK (' . $minAmount . ')</font>';
        else
            return '<font color="red">NOK (' . $minAmount . ')</font>';
    }

    /**
     * Format a value in base currency
     * @param <type> $value
     */
    public function formatCurrency($value)
    {
        return Mage::app()->getStore()->getBaseCurrency()->format($value);
    }

}