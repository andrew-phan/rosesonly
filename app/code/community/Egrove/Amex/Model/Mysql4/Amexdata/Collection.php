<?php

class Egrove_Amex_Model_Mysql4_Amexdata_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('amex/amexdata');
    }
}