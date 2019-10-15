<?php
    class Ant_Reminder_Model_Mysql4_Reminder_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
    {

		public function _construct(){
			$this->_init("reminder/reminder");
		}
    }
	 