<?php
    class Ant_Fbintegration_Model_Mysql4_Contact extends Mage_Core_Model_Mysql4_Abstract
    {
        protected function _construct()
        {
            $this->_init("fbintegration/contact", "contact_id");
        }
    }
	 