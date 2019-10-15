<?php
$installer = $this;
$installer->startSetup();
$installer->run("
CREATE TABLE `ant_delivery` (
  `assign_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `shipment_id` int(11) DEFAULT NULL,
  `increment_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `total_qty` int(11) DEFAULT NULL,
  `assigneddate` datetime DEFAULT NULL,
  `update_at` datetime DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `updatestatus` bit(1) DEFAULT b'1',
  `mw_customercomment_info` varchar(255) DEFAULT NULL,
  `mw_deliverydate_date` varchar(15) DEFAULT NULL,
  `mw_deliverydate_time` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`assign_id`)
);");
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 