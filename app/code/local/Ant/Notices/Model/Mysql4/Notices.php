<?php
    class Ant_Notices_Model_Mysql4_Notices extends Mage_Core_Model_Mysql4_Abstract
    {
        protected function _construct()
        {
            $this->_init("notices/notices", "notice_id");
        }
    }
	 