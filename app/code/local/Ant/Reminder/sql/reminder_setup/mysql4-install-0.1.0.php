<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
create table ant_reminder (
	ant_reminder_id int not null auto_increment, 
	customer_id int(10) unsigned, 
	occasion varchar(100), 
	title varchar(20),
	name varchar(100), 
	gender int(2) unsigned,	
	date date, 
	delivery_address varchar(255), 
	notification_type varchar(100),
	gift_type varchar(100),
	primary key(ant_reminder_id));

SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 