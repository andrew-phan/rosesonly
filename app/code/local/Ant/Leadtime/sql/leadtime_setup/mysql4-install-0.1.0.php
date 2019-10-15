<?php

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$setup->addAttribute('catalog_category', 'leadtime', array(
    'type' => 'int',
    'label' => 'Lead Time',
    'input' => 'text',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible' => true,
    'required' => false,
    'user_defined' => false,
    'default' => ''
));

$setup->addAttribute('catalog_category', 'enable_lead_time', array(
    'type' => 'int',
    'label' => 'Use group lead time',
    'input' => 'select',
    'source' => 'eav/entity_attribute_source_boolean',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'required' => false,
    'default' => 1,
    'user_defined' => false,
    'default' => 0
));
$setup->endSetup();
?>