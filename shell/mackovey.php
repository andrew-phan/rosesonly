<?php

require_once 'abstract.php';

class Mage_Shell_Compiler extends Mage_Shell_Abstract
{

    public function run()
    {
    echo trim(Mage::getStoreConfig('payment/amex/ama_pass'));
    }
}
$shell = new Mage_Shell_Compiler();
$shell->run();
