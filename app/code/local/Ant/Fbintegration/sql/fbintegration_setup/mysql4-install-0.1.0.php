<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
CREATE TABLE contact(
    contact_id int not null auto_increment, 
    customer_id int,
    facebook_id varchar(100), 
    name varchar(100),
    date datetime,
    delivery_address varchar(500),
    gift varchar(200),
    primary key(contact_id)
);
		
SQLTEXT;

$installer->run($sql);

$installer->endSetup();
	 