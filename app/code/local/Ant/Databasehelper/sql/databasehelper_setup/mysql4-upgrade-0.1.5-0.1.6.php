<?php
$installer = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();
$entityTypeId     = $installer->getEntityTypeId('catalog_category');
$attributeSetId   = $installer->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

/*
$installer->addAttribute('catalog_category', 'delivery_note_start_date',  array(
    'group'     => 'General',
    'type'      => 'datetime',
    'input'     => 'date',
    'visible'   => true,
    'label'     => 'Delivery Note Start Date',
    'global'    => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,    
    'required'          => false,
    'user_defined'      => false,
    'default'           => ''
));
$installer->addAttributeToGroup(
    $entityTypeId,
    $attributeSetId,
    $attributeGroupId,
    'delivery_note_start_date',
    '11'                    //last Magento's attribute position in General tab is 10
);
$attributeId = $installer->getAttributeId($entityTypeId, 'delivery_note_start_date');

$installer->run("
INSERT INTO `{$installer->getTable('catalog_category_entity_datetime')}`
(`entity_type_id`, `attribute_id`, `entity_id`, `value`)
    SELECT '{$entityTypeId}', '{$attributeId}', `entity_id`, '1'
        FROM `{$installer->getTable('catalog_category_entity')}`;
");
        
$installer->addAttribute('catalog_category', 'delivery_note_end_date',  array(
    'group'     => 'General',
    'type'      => 'datetime',
    'input'     => 'date',
    'visible'   => true,
    'label'     => 'Delivery Note End Date',
    'global'    => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,    
    'required'          => false,
    'user_defined'      => false,
    'default'           => ''
));
$installer->addAttributeToGroup(
    $entityTypeId,
    $attributeSetId,
    $attributeGroupId,
    'delivery_note_end_date',
    '11'                    //last Magento's attribute position in General tab is 10
);

$attributeId = $installer->getAttributeId($entityTypeId, 'delivery_note_end_date');

$installer->run("
INSERT INTO `{$installer->getTable('catalog_category_entity_datetime')}`
(`entity_type_id`, `attribute_id`, `entity_id`, `value`)
    SELECT '{$entityTypeId}', '{$attributeId}', `entity_id`, '1'
        FROM `{$installer->getTable('catalog_category_entity')}`;
");
    

//------------------------------------------------------------
$installer->addAttribute('catalog_category', 'earliest_delivery_date',  array(
    'group'     => 'General',
    'type'      => 'datetime',
    'input'     => 'date',
    'visible'   => true,
    'label'     => 'Earliest Delivery Date',
    'global'    => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,    
    'required'          => false,
    'user_defined'      => false,
    'default'           => ''
));
$installer->addAttributeToGroup(
    $entityTypeId,
    $attributeSetId,
    $attributeGroupId,
    'earliest_delivery_date',
    '11'                    //last Magento's attribute position in General tab is 10
);

$attributeId = $installer->getAttributeId($entityTypeId, 'earliest_delivery_date');

$installer->run("
INSERT INTO `{$installer->getTable('catalog_category_entity_datetime')}`
(`entity_type_id`, `attribute_id`, `entity_id`, `value`)
    SELECT '{$entityTypeId}', '{$attributeId}', `entity_id`, '1'
        FROM `{$installer->getTable('catalog_category_entity')}`;
");
 
////------------------------------------------------------------
$installer->addAttribute('catalog_category', 'earliest_delivery_time',  array(
    'group'     => 'General',
    'type'              => 'varchar',
    'input'             => 'select',
    'visible'   => true,
    'label'     => 'Earliest Delivery Time',
    'global'    => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,    
    'required'          => false,
    'user_defined'      => false,
    'default'           => '',
    'option' => array(
        'value' => array( 
            'optionone' => array( '9:00am - 1:00pm' ),
            'optiontwo' => array( '2:00pm - 5:30pm' ),
            'optionthree' => array( '7:00pm - 11:00pm' ),
        )
    ),
));
$installer->addAttributeToGroup(
    $entityTypeId,
    $attributeSetId,
    $attributeGroupId,
    'earliest_delivery_time',
    '11'                    //last Magento's attribute position in General tab is 10
);

$attributeId = $installer->getAttributeId($entityTypeId, 'earliest_delivery_time');

$installer->run("
INSERT INTO `{$installer->getTable('catalog_category_entity_text')}`
(`entity_type_id`, `attribute_id`, `entity_id`, `value`)
    SELECT '{$entityTypeId}', '{$attributeId}', `entity_id`, '1'
        FROM `{$installer->getTable('catalog_category_entity')}`;
");
        
//------------------------------------------------------------
$installer->addAttribute('catalog_category', 'earliest_delivery_start',  array(
    'group'     => 'General',
    'type'      => 'datetime',
    'input'     => 'date',
    'visible'   => true,
    'label'     => 'Earliest Delivery Start',
    'global'    => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,    
    'required'          => false,
    'user_defined'      => false,
    'default'           => ''
));
$installer->addAttributeToGroup(
    $entityTypeId,
    $attributeSdelivery_noteetId,
    $attributeGroupId,
    'earliest_delivery_start',
    '11'                    //last Magento's attribute position in General tab is 10
);

$attributeId = $installer->getAttributeId($entityTypeId, 'earliest_delivery_start');

$installer->run("
INSERT INTO `{$installer->getTable('catalog_category_entity_datetime')}`
(`entity_type_id`, `attribute_id`, `entity_id`, `value`)
    SELECT '{$entityTypeId}', '{$attributeId}', `entity_id`, '1'
        FROM `{$installer->getTable('catalog_category_entity')}`;
");
 */
//-----------------------------------------------------------
////------------------------------------------------------------
$installer->addAttribute('catalog_category', 'earliest_delivery_end',  array(
    'group'     => 'General',
    'type'      => 'datetime',
    'input'     => 'date',
    'visible'   => true,
    'label'     => 'Earliest Delivery End',
    'global'    => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,    
    'required'          => false,
    'user_defined'      => false,
    'default'           => ''
));
$installer->addAttributeToGroup(
    $entityTypeId,
    $attributeSetId,
    $attributeGroupId,
    'earliest_delivery_end',
    '11'                    //last Magento's attribute position in General tab is 10
);

$attributeId = $installer->getAttributeId($entityTypeId, 'earliest_delivery_end');

$installer->run("
INSERT INTO `{$installer->getTable('catalog_category_entity_datetime')}`
(`entity_type_id`, `attribute_id`, `entity_id`, `value`)
    SELECT '{$entityTypeId}', '{$attributeId}', `entity_id`, '1'
        FROM `{$installer->getTable('catalog_category_entity')}`;
");
        

        
//this will set data of your custom attribute for root category
Mage::getModel('catalog/category')
    ->load(1)
    ->setImportedCatId(0)
    ->setInitialSetupFlag(true)
    ->save();
//this will set data of your custom attribute for default category
Mage::getModel('catalog/category')
    ->load(2)
    ->setImportedCatId(0)
    ->setInitialSetupFlag(true)
    ->save();
$installer->endSetup();