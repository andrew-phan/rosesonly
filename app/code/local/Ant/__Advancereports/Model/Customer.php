<?php

class Ant_Advancereports_Model_Customer extends Mage_Reports_Model_Mysql4_Order_Collection {

    function __construct() {     
        parent::__construct();        
        $this->_init('sales/order', 'entity_id');
    }
}

?>