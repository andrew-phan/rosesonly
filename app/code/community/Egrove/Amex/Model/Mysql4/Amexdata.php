<?php

class Egrove_Amex_Model_Mysql4_Amexdata extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('amex/amexdata', 'id');
    }
}