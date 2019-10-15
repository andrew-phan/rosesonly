<?php

include_once("Mage/Customer/Model/Customer.php");
class Ant_HHonors_Model_HHonors extends Mage_Customer_Model_Customer {
    
    public function getHHonorsNumber()
    {
        return $this->_getData('hhonors_number');
    }
    
    public function getHHonorsBonusCode()
    {
        return $this->_getData('hhonors_bonus_code');
    }
    
    public function getHHonorsPoint()
    {
        return $this->_getData('hhonors_point');
    }
    
}

?>