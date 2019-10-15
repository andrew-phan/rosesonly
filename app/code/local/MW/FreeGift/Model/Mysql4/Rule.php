<?php

class MW_FreeGift_Model_Mysql4_Rule extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the freegift_id refers to the key field in your database table.
        $this->_init('freegift/rule', 'rule_id');
    }
}