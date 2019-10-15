<?php
    class Ant_Latepayment_Model_Mysql4_Latepayment extends Mage_Core_Model_Mysql4_Abstract
    {
        protected function _construct()
        {
            $this->_init("latepayment/latepayment", "entity_id");
        }
    }
	 