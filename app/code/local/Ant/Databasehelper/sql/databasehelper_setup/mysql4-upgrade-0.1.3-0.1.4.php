<?php

$installer = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();

$sql=<<<SQLTEXT

INSERT INTO `eav_attribute` (`entity_type_id`, `attribute_code`, `backend_model`, `backend_type`, `backend_table`, `frontend_model`, `frontend_input`, `frontend_label`, `frontend_class`, `source_model`, `is_required`, `is_user_defined`, `default_value`, `is_unique`, `note`) 
VALUES ('1', 'company', NULL, 'varchar', NULL, NULL, 'text', 'Company', NULL, NULL, '0', '1', NULL, '0', NULL);

INSERT INTO `customer_eav_attribute` (`attribute_id`) 
VALUES ((SELECT `attribute_id` 
		FROM `eav_attribute`
        WHERE `entity_type_id` = 1 
        AND `attribute_code` = "company"));

INSERT INTO `customer_form_attribute` (`form_code` , `attribute_id` ) 
VALUES ( 'adminhtml_customer', (SELECT `attribute_id` 
				FROM `eav_attribute`
				WHERE `entity_type_id` = 1 
				AND `attribute_code` = "company") );
			   
INSERT INTO `customer_form_attribute` (`form_code` , `attribute_id` ) 
VALUES ( 'checkout_register', (SELECT `attribute_id` 
                                FROM `eav_attribute`
                                WHERE `entity_type_id` = 1 
				AND `attribute_code` = "company") );

INSERT INTO `customer_form_attribute` (`form_code` , `attribute_id` ) 
VALUES ( 'customer_account_create', (SELECT `attribute_id` 
                                        FROM `eav_attribute`
					WHERE `entity_type_id` = 1 
					AND `attribute_code` = "company") );
			   
INSERT INTO `customer_form_attribute` (`form_code` , `attribute_id` ) 
VALUES ( 'customer_account_edit', (SELECT `attribute_id` 
                                    FROM `eav_attribute`
                                    WHERE `entity_type_id` = 1 
                                    AND `attribute_code` = "company"));

SQLTEXT;
$installer->run($sql);

$company_attribute = Mage::getSingleton("eav/config")->getAttribute("customer", "company");
$company_attribute->setData("sort_order", 200);
$company_attribute->save();

$installer->endSetup();
