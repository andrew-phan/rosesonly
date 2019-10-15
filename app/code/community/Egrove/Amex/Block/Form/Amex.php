<?php
class Egrove_Amex_Block_Form_Amex extends Mage_Payment_Block_Form_Ccsave
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('amex/ccsave.phtml');
    }
}