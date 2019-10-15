<?php
$installer = $this;
$installer->startSetup();
$installer->run("
ALTER TABLE `ant_delivery` DROP COLUMN 'increment_id';
ALTER TABLE `ant_delivery` ADD 'increment_id' VARCHAR(20) AFTER `shipment_id`;
");
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 