<?php

$installer = $this;

$installer->startSetup();
$collection =Mage::getModel('onestepcheckout/onestepcheckout')->getCollection();
$installer->run("
        
    ALTER TABLE {$collection->getTable('onestepcheckout')}
    ADD COLUMN `print_do` BOOLEAN DEFAULT FALSE;
    
    ALTER TABLE {$collection->getTable('onestepcheckout')}
    ADD COLUMN `print_msg` BOOLEAN DEFAULT FALSE;
    ");
//
$installer->endSetup(); 