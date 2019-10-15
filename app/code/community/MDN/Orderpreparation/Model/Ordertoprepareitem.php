<?php

/**
 * Classe service pour la pr�paration des commandes
 *
 */
class MDN_Orderpreparation_Model_OrderToPrepareItem extends Mage_Core_Model_Abstract {

    private $_SelectedOrders = null;
    private $_salesOrderItem = null;

    /*     * ***************************************************************************************************************************
     * ***************************************************************************************************************************
     * Constructeur
     *
     */

    public function _construct() {
        parent::_construct();
        $this->_init('Orderpreparation/ordertoprepareitem');
    }

    public function getSalesOrderItem() {
        if ($this->_salesOrderItem == null)
        {
            $id = $this->getorder_item_id();
            $this->_salesOrderItem = mage::getModel('sales/order_item')->load($id);
        }
        return $this->_salesOrderItem;
    }

}
