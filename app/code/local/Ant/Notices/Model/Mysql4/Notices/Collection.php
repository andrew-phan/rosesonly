<?php
    class Ant_Notices_Model_Mysql4_Notices_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
    {

		public function _construct(){
			$this->_init("notices/notices");
		}
    }
	 