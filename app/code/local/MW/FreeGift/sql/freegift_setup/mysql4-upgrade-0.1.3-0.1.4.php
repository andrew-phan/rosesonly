<?php

$installer = $this;
$installer->startSetup();
$sql = "DROP TABLE IF EXISTS {$installer->getTable('freegift/product_attribute')};
CREATE TABLE IF NOT EXISTS `{$installer->getTable('freegift/product_attribute')}` (
  `rule_id` int(10) unsigned NOT NULL,
  `website_id` smallint(5) unsigned NOT NULL,
  `customer_group_id` smallint(5) unsigned NOT NULL,
  `attribute_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`rule_id`,`website_id`,`customer_group_id`,`attribute_id`),
  KEY `IDX_WEBSITE` (`website_id`),
  KEY `IDX_CUSTOMER_GROUP` (`customer_group_id`),
  KEY `IDX_ATTRIBUTE` (`attribute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";
$installer->run($sql);
$installer->endSetup();