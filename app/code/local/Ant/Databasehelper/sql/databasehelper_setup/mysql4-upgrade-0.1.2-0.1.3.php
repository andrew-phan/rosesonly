<?php
//Change the lable of customer address telephone from Telephone to Mobile
$installer = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();

$office_tel_attribute = Mage::getSingleton("eav/config")->getAttribute("customer", "officetel");
$office_tel_attribute->setData("sort_order", 200);
$office_tel_attribute->save();

$mobile_tel_attribute = Mage::getSingleton("eav/config")->getAttribute("customer", "telephone");
$mobile_tel_attribute->setData("sort_order", 200);
$mobile_tel_attribute->save();

$home_tel_attribute = Mage::getSingleton("eav/config")->getAttribute("customer", "hometel");
$home_tel_attribute->setData("sort_order", 200);
$home_tel_attribute->save();

$occupation_attribute = Mage::getSingleton("eav/config")->getAttribute("customer", "occupation");
$occupation_attribute->setData("sort_order", 200);
$occupation_attribute->save();

$installer->endSetup();