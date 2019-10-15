<?php

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('amexdata')};
CREATE TABLE {$this->getTable('amexdata')} (
  `id` int(11) unsigned NOT NULL auto_increment,
  `amount` varchar(255) NOT NULL default '',
  `order_id` varchar(255) NOT NULL default '',
  `authorized_id` varchar(255) NOT NULL default '',
  `message` varchar(255) NOT NULL default '',
  `capture_message` varchar(255) NOT NULL default '',
  `transation_no` varchar(255) NOT NULL default '',
  `capture_amount` varchar(255) NOT NULL default '',
  `capture_tno` varchar(255) NOT NULL default '',
  `capture_rno` varchar(255) NOT NULL default '',
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


    ");

$installer->endSetup(); 