<?php

$installer = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();

$installer->addAttribute("customer_address", "office_tel", array(
    "type" => "varchar",
    "backend" => "",
    "label" => "Office Tel",
    "input" => "text",
    "source" => "",
    "visible" => true,
    "required" => false,
    "default" => "",
    "frontend" => "",
    "unique" => false,
    "note" => ""
));

$attribute = Mage::getSingleton("eav/config")->getAttribute("customer_address", "office_tel");

Mage::log($attribute->getData());
$used_in_forms = array();

$used_in_forms[] = "adminhtml_customer_address";
$used_in_forms[] = "customer_register_address";
$used_in_forms[] = "customer_address_edit";
$attribute->setData("used_in_forms", $used_in_forms)
        ->setData("is_used_for_customer_segment", true)
        ->setData("is_system", 0)
        ->setData("is_user_defined", 1)
        ->setData("is_visible", 1)
        ->setData("sort_order", 100)
;
$attribute->save();

$installer->addAttribute("customer_address", "home_tel", array(
    "type" => "varchar",
    "backend" => "",
    "label" => "Home Tel",
    "input" => "text",
    "source" => "",
    "visible" => true,
    "required" => false,
    "default" => "",
    "frontend" => "",
    "unique" => false,
    "note" => ""
));

$attribute = Mage::getSingleton("eav/config")->getAttribute("customer_address", "home_tel");


$used_in_forms = array();

$used_in_forms[] = "adminhtml_customer_address";
$used_in_forms[] = "customer_register_address";
$used_in_forms[] = "customer_address_edit";
$attribute->setData("used_in_forms", $used_in_forms)
        ->setData("is_used_for_customer_segment", true)
        ->setData("is_system", 0)
        ->setData("is_user_defined", 1)
        ->setData("is_visible", 1)
        ->setData("sort_order", 100)
;
$attribute->save();



$installer->endSetup();

