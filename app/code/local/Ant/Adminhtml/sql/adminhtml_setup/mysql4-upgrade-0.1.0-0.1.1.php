<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of mysql4-upgrade-0
 *
 * @author Quy Cao
 */
$installer = $this;
$installer->startSetup();

Mage::log('Start Inserting new colum to rule');
//$installer->run("
//    
//ALTER TABLE {$this->getTable('catalogrule')}
//    ADD COLUMN `promotionimage` varchar(255) NOT NULL default '';
//    
//ALTER TABLE {$this->getTable('catalogrule')}
//    ADD COLUMN `filethumbgrid` text;
//    
//ALTER TABLE {$this->getTable('salesrule')}
//    ADD COLUMN `promotionimage` varchar(255) NOT NULL default '';
//    
//ALTER TABLE {$this->getTable('salesrule')}
//    ADD COLUMN `filethumbgrid` text;
//    
//");

$installer->endSetup();

?>
