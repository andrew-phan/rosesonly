<?php

$installer = $this;
$installer->startSetup();

Mage::log('Start Inserting new colum to rule');
$installer->run("
    
ALTER TABLE {$this->getTable('catalogrule')}
    ADD COLUMN `qty_limited` int NOT NULL default '0';
    
ALTER TABLE {$this->getTable('catalogrule')}
    ADD COLUMN `min_monetary` double NOT NULL default '0';  
    
ALTER TABLE {$this->getTable('salesrule')}
    ADD COLUMN `qty_limited` int NOT NULL default '0';
    
ALTER TABLE {$this->getTable('salesrule')}
    ADD COLUMN `min_monetary` double NOT NULL default '0';  
      
");


$installer->endSetup();

