<?php
//Change the lable of customer address telephone from Telephone to Mobile
$installer = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();
$sql=<<<SQLTEXT
UPDATE `eav_attribute` 
    SET `frontend_label` = "Mobile"
    WHERE `attribute_code` = "telephone"
    AND `entity_type_id` IN (   SELECT `entity_type_id` 
                                FROM `eav_entity_type`
                                WHERE `entity_type_code` = "customer_address"
                                );		
SQLTEXT;
$installer->run($sql);

$attribute = Mage::getSingleton("eav/config")->getAttribute("customer_address", "fax");
$attribute->setData("sort_order", 99)
;
$attribute->save();

$installer->endSetup();