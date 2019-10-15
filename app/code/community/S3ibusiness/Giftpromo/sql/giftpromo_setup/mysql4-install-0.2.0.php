<?php

$installer = $this;

$installer->startSetup();
/*
$installer->run("

ALTER TABLE `giftpromo`
ADD `qty` int(11) unsigned NOT NULL default 1 AFTER `status`

    ");
*/
$installer->endSetup();
