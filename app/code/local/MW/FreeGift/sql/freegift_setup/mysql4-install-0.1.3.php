<?php

$installer = $this;

$installer->startSetup();
$installer->run("

DROP TABLE IF EXISTS {$installer->getTable('freegift/product')};
CREATE TABLE IF NOT EXISTS `{$installer->getTable('freegift/product')}` (
  `rule_product_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rule_id` int(10) unsigned NOT NULL DEFAULT '0',
  `product_id` int(10) unsigned NOT NULL DEFAULT '0',
  `from_date` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `stop_rules_processing` tinyint(1) NOT NULL DEFAULT '0',
  `discount_qty` int(10) unsigned NOT NULL DEFAULT '0',
  `times_used` int(10) unsigned NOT NULL DEFAULT '0',
  `customer_group_ids` text,
  `website_ids` text,
  `gift_product_ids` mediumtext NOT NULL,
  `sort_order` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`rule_product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS {$installer->getTable('freegift/rule')};
CREATE TABLE IF NOT EXISTS `{$installer->getTable('freegift/rule')}` (
  `rule_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `from_date` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `customer_group_ids` text,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `discount_qty` int(10) unsigned NOT NULL,
  `times_used` int(10) unsigned NOT NULL,
  `conditions_serialized` mediumtext NOT NULL,
  `stop_rules_processing` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int(10) unsigned NOT NULL DEFAULT '0',
  `simple_action` varchar(32) NOT NULL,
  `gift_product_ids` mediumtext NOT NULL,
  `website_ids` text,
  PRIMARY KEY (`rule_id`),
  KEY `sort_order` (`is_active`,`sort_order`,`to_date`,`from_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS {$installer->getTable('freegift/salesrule')};
CREATE TABLE IF NOT EXISTS `{$installer->getTable('freegift/salesrule')}` (
  `rule_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `from_date` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `customer_group_ids` text,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `discount_qty` int(10) unsigned NOT NULL,
  `coupon_code` varchar(255) NOT NULL DEFAULT '',
  `times_used` int(10) unsigned NOT NULL,
  `conditions_serialized` mediumtext NOT NULL,
  `stop_rules_processing` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int(10) unsigned NOT NULL DEFAULT '0',
  `gift_product_ids` mediumtext NOT NULL,
  `website_ids` text,
  PRIMARY KEY (`rule_id`),
  KEY `sort_order` (`is_active`,`sort_order`,`to_date`,`from_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE `{$installer->getTable('sales/quote')}` 
ADD `freegift_coupon_code` VARCHAR( 255 ) NOT NULL DEFAULT '' AFTER `coupon_code`,
ADD `freegift_applied_rule_ids` VARCHAR( 255 ) NOT NULL DEFAULT '' AFTER `applied_rule_ids`,
ADD `freegift_ids` VARCHAR( 255 ) NOT NULL DEFAULT '' AFTER `freegift_applied_rule_ids`;

    ");

$installer->endSetup(); 