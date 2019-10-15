<?php
$installer = $this;
$installer->startSetup();

$installer->addAttribute("order", "order_hhonors_number", array("type"=>"varchar"));
$installer->addAttribute("order", "order_hhonors_bonus_code", array("type"=>"varchar"));
$installer->endSetup();
	 