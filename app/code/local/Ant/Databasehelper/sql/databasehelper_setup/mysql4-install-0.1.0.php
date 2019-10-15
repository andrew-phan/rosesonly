<?php
$installer = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();
//remove taxvat attribute from customer
$installer->removeAttribute('customer', 'taxvat');
$installer->endSetup();
	 