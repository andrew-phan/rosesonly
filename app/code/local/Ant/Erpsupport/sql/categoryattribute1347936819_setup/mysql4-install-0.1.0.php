<?php
$installer = $this;
$installer->startSetup();
//*****This code will create catalog attribute, this code support for ERP plug-in
//$installer->addAttribute('catalog_product','waiting_for_delivery_qty', array(
//                        'type'              => 'int',
//                        'backend'           => '',
//                        'frontend'          => '',
//                        'label'             => 'Waiting for delivery qty',
//                        'input'             => 'select',
//                        'class'             => '',
//                        'source'            => 'eav/entity_attribute_source_boolean',
//                        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
//                        'visible'           => false,
//                        'required'          => false,
//                        'user_defined'      => false,
//                        'default'           => '0',
//                        'searchable'        => false,
//                        'filterable'        => false,
//                        'comparable'        => false,
//                        'visible_on_front'  => false,
//                        'unique'            => false
//						));	
//$installer->addAttribute('catalog_product','manual_supply_need_date', array(
//                        'type'              => 'text',
//                        'backend'           => '',
//                        'frontend'          => '',
//                        'label'             => 'Manual supply need date',
//                        'input'             => 'text',
//                        'class'             => '',
//                        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
//                        'visible'           => false,
//                        'required'          => false,
//                        'user_defined'      => false,
//                        'default'           => '',
//                        'searchable'        => false,
//                        'filterable'        => false,
//                        'comparable'        => false,
//                        'visible_on_front'  => false,
//                        'unique'            => false
//						));	
//$installer->addAttribute('catalog_product','manual_supply_need_qty', array(
//                        'type'              => 'int',
//                        'backend'           => '',
//                        'frontend'          => '',
//                        'label'             => 'Manual supply need qty',
//                        'input'             => 'select',
//                        'class'             => '',
//                        'source'            => 'eav/entity_attribute_source_boolean',
//                        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
//                        'visible'           => false,
//                        'required'          => false,
//                        'user_defined'      => false,
//                        'default'           => '0',
//                        'searchable'        => false,
//                        'filterable'        => false,
//                        'comparable'        => false,
//                        'visible_on_front'  => false,
//                        'unique'            => false
//						));	
//
//$installer->addAttribute('catalog_product','manual_supply_need_comments', array(
//                        'type'              => 'text',
//                        'backend'           => '',
//                        'frontend'          => '',
//                        'label'             => 'Manual supply need comments',
//                        'input'             => 'textarea',
//                        'class'             => '',
//                        'source'            => 'eav/entity_attribute_source_boolean',
//                        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
//                        'visible'           => false,
//                        'required'          => false,
//                        'user_defined'      => false,
//                        'default'           => '',
//                        'searchable'        => false,
//                        'filterable'        => false,
//                        'comparable'        => false,
//                        'visible_on_front'  => false,
//                        'unique'            => false
//						));	
//$installer->addAttribute('catalog_product','override_subproducts_planning', array(
//                        'type'              => 'int',
//                        'backend'           => '',
//                        'frontend'          => '',
//                        'label'             => 'Override subproducts planning',
//                        'input'             => 'select',
//                        'class'             => '',
//                        'source'            => 'eav/entity_attribute_source_boolean',
//                        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
//                        'visible'           => true,
//                        'required'          => false,
//                        'user_defined'      => false,
//                        'default'           => '',
//                        'searchable'        => false,
//                        'filterable'        => false,
//                        'comparable'        => false,
//                        'visible_on_front'  => false,
//                        'unique'            => false,
//                        'apply_to'          => 'bundle,configurable'
//						));	
//$installer->addAttribute('catalog_product','purchase_tax_rate', array(
//															'type' 		=> 'int',
//															'visible' 	=> false,
//															'label'		=> 'Purchase Tax Rate',
//															'required'  => false,
//															'global'       => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL
//															));
//$installer->addAttribute('catalog_product','exclude_from_supply_needs', array(
//                        'type'              => 'static',
//                        'backend'           => '',
//                        'frontend'          => '',
//                        'label'             => 'exclude_from_supply_needs',
//                        'input'             => 'text',
//                        'class'             => '',
//                        'source'            => '',
//                        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
//                        'visible'           => false,
//                        'required'          => true,
//                        'user_defined'      => false,
//                        'default'           => '0',
//                        'searchable'        => false,
//                        'filterable'        => false,
//                        'comparable'        => false,
//                        'visible_on_front'  => false,
//                        'visible_in_advanced_search' => false,
//                        'unique'            => false
//															));
//$installer->addAttribute('catalog_product','default_supply_delay', array(
//															'type' 		=> 'int',
//															'visible' 	=> false,
//															'label'		=> 'Default Supply delay',
//															'required'  => false,
//															'default'   => '5',
//															'global'       => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
//															'note'		=> 'In days'
//															));
//						
////rajoute l'attribut supply_date au produit (prochaine date d'approvisionnement)
//$installer->addAttribute('catalog_product','supply_date', array(
//															'type' 		=> 'datetime',
//															'visible' 	=> false,
//															'label'		=> 'Supply date',
//															'required'  => false,
//															'global'       => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE
//															));
//$installer->addAttribute('catalog_product','ordered_qty', array(
//                                                                'type' 		=> 'int',
//                                                                'visible' 	=> false,
//                                                                'label'		=> 'Ordered Qty',
//                                                                'required'  => false,
//                                                                'default'   => '0',
//                                                                'global'       => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
//                                                                'note'		=> 'Qty included in pending orders'
//                                                                ));
//															
////rajoute l'attribut reserved_qty au produit
//$installer->addAttribute('catalog_product','reserved_qty', array(
//                                                                'type' 		=> 'int',
//                                                                'visible' 	=> false,
//                                                                'label'		=> 'Reserved Qty',
//                                                                'required'  => false,
//                                                                'default'   => '0',
//                                                                'global'    => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
//                                                                ));
//$installer->addAttribute('catalog_product','outofstock_period_enabled', array(
//                        'type'              => 'int',
//                        'backend'           => '',
//                        'frontend'          => '',
//                        'label'             => 'Out of stock period',
//                        'input'             => 'select',
//                        'class'             => '',
//                        'source'            => 'eav/entity_attribute_source_boolean',
//                        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
//                        'visible'           => false,
//                        'required'          => false,
//                        'user_defined'      => false,
//                        'default'           => '0',
//                        'searchable'        => false,
//                        'filterable'        => false,
//                        'comparable'        => false,
//                        'visible_on_front'  => false,
//                        'unique'            => false
//						));	
//
//$installer->addAttribute('catalog_product','outofstock_period_from', array(
//                        'type'              => 'text',
//                        'backend'           => '',
//                        'frontend'          => '',
//                        'label'             => 'Out of stock From',
//                        'input'             => 'text',
//                        'class'             => '',
//                        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
//                        'visible'           => false,
//                        'required'          => false,
//                        'user_defined'      => false,
//                        'searchable'        => false,
//                        'filterable'        => false,
//                        'comparable'        => false,
//                        'visible_on_front'  => false,
//                        'unique'            => false
//						));
//$installer->addAttribute('catalog_product','outofstock_period_to', array(
//                        'type'              => 'text',
//                        'backend'           => '',
//                        'frontend'          => '',
//                        'label'             => 'Out of stock To',
//                        'input'             => 'text',
//                        'class'             => '',
//                        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
//                        'visible'           => false,
//                        'required'          => false,
//                        'user_defined'      => false,
//                        'searchable'        => false,
//                        'filterable'        => false,
//                        'comparable'        => false,
//                        'visible_on_front'  => false,
//                        'unique'            => false
//						));		
//// Adtribute nay cua Quotation
//$installer->addAttribute('catalog_product','allow_individual_quote_request', array(
//															'type' 		=> 'int',
//															'visible' 	=> true,
//															'label'		=> 'Allow individual quote request',
//															'required'  => false,
//															'default'   => '0',
//															'global'    => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
//															'apply_to'  => 'simple',
//									                        'input'     => 'select',
//									                        'class'     => '',
//									                        'source'    => 'eav/entity_attribute_source_boolean',
//									                        'used_in_product_listing'	=> true
//															));
//
//$installer->addAttribute('catalog_product','is_quotation', array(
//															'type' 		=> 'int',
//															'visible' 	=> false,
//															'label'		=> 'Is Quotation',
//															'required'  => false,
//															'default'   => '0',
//															'global'       => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,													        
//															'backend'           => '',
//													        'frontend'          => '',													        
//													        'input'             => '',
//													        'class'             => '',
//													        'source'            => '',
//													        'user_defined'      => false,
//													        'searchable'        => false,
//													        'filterable'        => false,
//													        'comparable'        => false,
//													        'visible_on_front'  => false,
//															'is_configurable' => false,
//													        'unique'            => false															
//));
//
//$installer->addAttribute('catalog_product','quotation_id', array(
//															'type' 		=> 'int',
//															'visible' 	=> false,
//															'label'		=> 'Quotation Id',
//															'required'  => false,
//															'default'   => '0',
//															'global'       => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
//															));
//
//$installer->addAttribute('catalog_product','outofstock_period_enabled', array(
//                        'type'              => 'int',
//                        'backend'           => '',
//                        'frontend'          => '',
//                        'label'             => 'Out of stock period',
//                        'input'             => 'select',
//                        'class'             => '',
//                        'source'            => 'eav/entity_attribute_source_boolean',
//                        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
//                        'visible'           => false,
//                        'required'          => false,
//                        'user_defined'      => false,
//                        'default'           => '0',
//                        'searchable'        => false,
//                        'filterable'        => false,
//                        'comparable'        => false,
//                        'visible_on_front'  => false,
//                        'unique'            => false
//						));	
//
//$installer->addAttribute('catalog_product','outofstock_period_from', array(
//                        'type'              => 'text',
//                        'backend'           => '',
//                        'frontend'          => '',
//                        'label'             => 'Out of stock From',
//                        'input'             => 'text',
//                        'class'             => '',
//                        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
//                        'visible'           => false,
//                        'required'          => false,
//                        'user_defined'      => false,
//                        'searchable'        => false,
//                        'filterable'        => false,
//                        'comparable'        => false,
//                        'visible_on_front'  => false,
//                        'unique'            => false
//						));							
//						
//$installer->addAttribute('catalog_product','outofstock_period_to', array(
//                        'type'              => 'text',
//                        'backend'           => '',
//                        'frontend'          => '',
//                        'label'             => 'Out of stock To',
//                        'input'             => 'text',
//                        'class'             => '',
//                        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
//                        'visible'           => false,
//                        'required'          => false,
//                        'user_defined'      => false,
//                        'searchable'        => false,
//                        'filterable'        => false,
//                        'comparable'        => false,
//                        'visible_on_front'  => false,
//                        'unique'            => false
//						));
//
//$installer->run(
//"--
//-- Structure for view `erp_view_supplyneeds_base`
//--
//DROP TABLE IF EXISTS `erp_view_supplyneeds_base`;
//create or replace view
//        erp_view_supplyneeds_base
//    AS
//    select
//        tbl_stock_item.product_id,
//        tbl_stock_item.stock_id,
//        tbl_product.sku,
//        tbl_name.value as name,
//        tbl_manufacturer.value manufacturer_id,
//        tbl_stock_item.qty as stock,
//        if (tbl_stock_item.qty > tbl_stock_item.stock_ordered_qty, tbl_stock_item.qty - tbl_stock_item.stock_ordered_qty, 0) as available_qty,
//        if (use_config_notify_stock_qty = 1, CONVERT(tbl_config_notify.value, signed), notify_stock_qty) as warning_stock_level,
//        if (use_config_ideal_stock_level = 1, CONVERT(tbl_config_ideal.value, signed), ideal_stock_level) as ideal_stock_level,
//        if (tbl_waiting_for_delivery_qty.value, tbl_waiting_for_delivery_qty.value, 0) as waiting_for_delivery_qty,
//        if (tbl_manual_supply_needs.value, tbl_manual_supply_needs.value, 0) as manual_supply_needs_qty,
//        if (tbl_stock_item.qty > tbl_stock_item.stock_ordered_qty_for_valid_orders, 0, tbl_stock_item.stock_ordered_qty_for_valid_orders - tbl_stock_item.qty) as qty_needed_for_valid_orders,
//        if (tbl_stock_item.qty > tbl_stock_item.stock_ordered_qty, 0, tbl_stock_item.stock_ordered_qty - tbl_stock_item.qty) as qty_needed_for_orders,
//        if (if (tbl_stock_item.qty > tbl_stock_item.stock_ordered_qty, tbl_stock_item.qty - tbl_stock_item.stock_ordered_qty, 0) < if (use_config_notify_stock_qty = 1, CONVERT(tbl_config_notify.value, signed), notify_stock_qty), if (use_config_ideal_stock_level = 1, CONVERT(tbl_config_ideal.value, signed), ideal_stock_level) - if (tbl_stock_item.qty > tbl_stock_item.stock_ordered_qty, tbl_stock_item.qty - tbl_stock_item.stock_ordered_qty, 0), 0) as qty_needed_for_ideal_stock,
//        if (tbl_manual_supply_needs.value > if (tbl_stock_item.qty > tbl_stock_item.stock_ordered_qty, tbl_stock_item.qty - tbl_stock_item.stock_ordered_qty, 0),tbl_manual_supply_needs.value  - if (tbl_stock_item.qty > tbl_stock_item.stock_ordered_qty, tbl_stock_item.qty - tbl_stock_item.stock_ordered_qty, 0) , 0) as qty_needed_for_manual_supply_needs
//
//    from
//        cataloginventory_stock_item tbl_stock_item
//        JOIN cataloginventory_stock tbl_stock on (tbl_stock_item.stock_id = tbl_stock.stock_id)
//        JOIN catalog_product_entity tbl_product on (tbl_stock_item.product_id = tbl_product.entity_id)
//        JOIN core_config_data tbl_config_notify on (1=1)
//        JOIN core_config_data tbl_config_ideal on (1=1)
//        JOIN core_config_data tbl_config_manufacturer on (1=1)
//        LEFT JOIN catalog_product_entity_int tbl_waiting_for_delivery_qty on (tbl_waiting_for_delivery_qty.entity_id = tbl_stock_item.product_id and tbl_waiting_for_delivery_qty.attribute_id = 973)
//        LEFT JOIN catalog_product_entity_int tbl_manual_supply_needs on (tbl_manual_supply_needs.entity_id = tbl_stock_item.product_id and tbl_manual_supply_needs.attribute_id = 970)
//        LEFT JOIN catalog_product_entity_int tbl_manufacturer on (tbl_manufacturer.entity_id = tbl_stock_item.product_id and tbl_manufacturer.attribute_id = tbl_config_manufacturer.value)
//        LEFT JOIN catalog_product_entity_varchar tbl_name on (tbl_name.entity_id = tbl_stock_item.product_id and tbl_name.attribute_id = 96 and tbl_name.store_id = 0)
//
//    where
//        tbl_stock.stock_disable_supply_needs <> 1
//        and ((tbl_stock_item.use_config_manage_stock = 1) or (tbl_stock_item.manage_stock = 1))
//        and tbl_product.exclude_from_supply_needs = 0
//        and tbl_config_notify.path = 'cataloginventory/item_options/notify_stock_qty'
//        and tbl_config_ideal.path = 'advancedstock/prefered_stock_level/ideal_stock_default_value'
//        and tbl_config_manufacturer.path = 'purchase/supplyneeds/manufacturer_attribute';
//
//-- --------------------------------------------------------
//
//--
//-- Structure for view `erp_view_supplyneeds_global`
//--
//DROP TABLE IF EXISTS `erp_view_supplyneeds_global`;
//create or replace view
//        erp_view_supplyneeds_global
//    AS
//    select
//        product_id,
//        manufacturer_id,
//        sku,
//        name,
//        waiting_for_delivery_qty,
//        if (
//            SUM(qty_needed_for_valid_orders) > 0 and (SUM(qty_needed_for_valid_orders) > waiting_for_delivery_qty ),
//            '1_valid_orders',
//            if (
//                SUM(qty_needed_for_orders) > 0 and (SUM(qty_needed_for_orders) - SUM(qty_needed_for_valid_orders) > (waiting_for_delivery_qty - SUM(qty_needed_for_valid_orders)) ),
//                '2_orders',
//                if (
//                    SUM(qty_needed_for_ideal_stock) > 0 and (SUM(qty_needed_for_orders) + SUM(qty_needed_for_ideal_stock) > waiting_for_delivery_qty),
//                    '3_prefered_stock_level',
//                    if (
//                        SUM(qty_needed_for_manual_supply_needs) > 0,
//                        '4_manual_supply_need',
//                        '5_pending_delivery'
//                       )
//                    )
//                )
//            )
//            as status,
//        if (SUM(qty_needed_for_valid_orders) - waiting_for_delivery_qty > 0, SUM(qty_needed_for_valid_orders) - waiting_for_delivery_qty, 0) as qty_min,
//        if (SUM(qty_needed_for_orders + qty_needed_for_ideal_stock + qty_needed_for_manual_supply_needs) - waiting_for_delivery_qty > 0, SUM(qty_needed_for_orders + qty_needed_for_ideal_stock + qty_needed_for_manual_supply_needs) - waiting_for_delivery_qty, 0) as qty_max
//    from
//        erp_view_supplyneeds_base
//    where
//        (qty_needed_for_valid_orders > 0 )
//        OR
//        (qty_needed_for_orders > 0)
//        OR
//        (qty_needed_for_ideal_stock > 0)
//        OR
//        (qty_needed_for_manual_supply_needs > 0)
//    group by
//        product_id,
//        manufacturer_id,
//        sku,
//        name;
//-- --------------------------------------------------------
//
//--
//-- Structure for view `erp_view_supplyneeds_warehouse`
//--
//DROP TABLE IF EXISTS `erp_view_supplyneeds_warehouse`;
//
//create view
//        erp_view_supplyneeds_warehouse
//    AS
//    select
//        stock_id,
//        product_id,
//        manufacturer_id,
//        sku,
//        name,
//        waiting_for_delivery_qty,
//        if (
//            (qty_needed_for_valid_orders) > 0 and ((qty_needed_for_valid_orders) > waiting_for_delivery_qty ),
//            '1_valid_orders',
//            if (
//                (qty_needed_for_orders) > 0 and ((qty_needed_for_orders) > (waiting_for_delivery_qty - (qty_needed_for_valid_orders)) ),
//                '2_orders',
//                if (
//                    (qty_needed_for_ideal_stock) > 0 and ((qty_needed_for_ideal_stock) > (waiting_for_delivery_qty - ((qty_needed_for_valid_orders)) + (qty_needed_for_orders)) ),
//                    '3_prefered_stock_level',
//                    if (
//                        (qty_needed_for_manual_supply_needs) > 0,
//                        '4_manual_supply_need',
//                        '5_pending_delivery'
//                       )
//                    )
//                )
//            )
//            as status,
//        qty_needed_for_valid_orders as qty_min,
//        (qty_needed_for_orders + qty_needed_for_ideal_stock + qty_needed_for_manual_supply_needs) as qty_max
//    from
//        erp_view_supplyneeds_base
//    where
//        (qty_needed_for_valid_orders > 0 )
//        OR
//        (qty_needed_for_orders > 0)
//        OR
//        (qty_needed_for_ideal_stock > 0)
//        OR
//        (qty_needed_for_manual_supply_needs > 0);
//");
//
//$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
//	$setup->addAttribute('order', 'mw_customercomment', array(
//	'label' => 'Customer Comment',
//	'type' => 'text',
//	'input' => 'text',
//	'visible' => true,
//	'required' => false,
//	'position' => 1,
//	));
//$setup->addAttribute('order', 'mw_customercomment', array(
//	'label' => 'Customer Comment',
//	'type' => 'text',
//	'input' => 'text',
//	'visible' => true,
//	'required' => false,
//	'position' => 1,
//	));
//$installer->run("
//
//DROP TABLE IF EXISTS {$this->getTable('mw_onestepcheckout')};
//CREATE TABLE {$this->getTable('mw_onestepcheckout')} (
//  `mw_onestepcheckout_date_id` int(11) unsigned NOT NULL auto_increment,
//  `sales_order_id` int(11) unsigned NOT NULL,
//  `mw_customercomment_info` varchar(255) default '',
//  `mw_deliverydate_date` varchar(15) default '',
//  `mw_deliverydate_time` varchar(10) default '',
//  `status` smallint(6) default '0',
//  `created_time` datetime NULL,
//  `update_time` datetime NULL,
//  PRIMARY KEY (`mw_onestepcheckout_date_id`)
//) ENGINE=InnoDB DEFAULT CHARSET=utf8;
//
//    ");
//	
//$collection =Mage::getModel('onestepcheckout/onestepcheckout')->getCollection();
//$installer->run("
//
//DROP TABLE IF EXISTS {$collection->getTable('onestepcheckout')};
//CREATE TABLE {$collection->getTable('onestepcheckout')} (
//  `mw_onestepcheckout_date_id` int(11) unsigned NOT NULL auto_increment,
//  `sales_order_id` int(11) unsigned NOT NULL,
//  `mw_customercomment_info` varchar(255) default '',
//  `mw_deliverydate_date` varchar(15) default '',
//  `mw_deliverydate_time` varchar(10) default '',
//  `status` smallint(6) default '0',
//  `created_time` datetime NULL,
//  `update_time` datetime NULL,
//  PRIMARY KEY (`mw_onestepcheckout_date_id`)
//) ENGINE=InnoDB DEFAULT CHARSET=utf8;
//
//    ");
//	
//$collection =Mage::getResourceModel('eav/entity_attribute_collection');
//$installer->run("
//
//	UPDATE {$collection->getTable('attribute')}
//		SET is_required =0
//		WHERE (entity_type_id =1 OR entity_type_id =2) 
//			AND (
//			attribute_code ='firstname' or 
//			attribute_code ='lastname' or 
//			attribute_code ='email'	or 
//			attribute_code ='country_id'  or 
//			attribute_code ='city' or 
//			attribute_code ='street' or 
//			attribute_code ='telephone' or 
//			attribute_code ='region_id' or 
//			attribute_code ='region' or 
//			attribute_code ='postcode' or 
//			attribute_code ='fax' or 
//			attribute_code ='company'
//			)
//    ");
//	
//$collection =Mage::getResourceModel('eav/entity_attribute_collection');
//$installer->run("
//
//	UPDATE {$collection->getTable('attribute')}
//		SET is_required =1
//		WHERE (entity_type_id =1 OR entity_type_id =2) 
//			AND (
//			attribute_code ='postcode' 
//			)
//    ");
//
////------------------------------------------------------------------------*******/
$installer->endSetup();
	 