<?php
$installer = $this;
$installer->startSetup();
$installer->run("create table notice(notice_id int not null auto_increment, title varchar(100), description text, primary key(notice_id));
		");
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 