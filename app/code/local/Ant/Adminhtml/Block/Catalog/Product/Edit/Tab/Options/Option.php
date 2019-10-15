<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * customers defined options
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Ant_Adminhtml_Block_Catalog_Product_Edit_Tab_Options_Option extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Options_Option {

    public function getOptionValues() {
        $optionsArr = array_reverse($this->getProduct()->getOptions(), true);
//        $optionsArr = $this->getProduct()->getOptions();

        if (!$this->_values) {
            $showPrice = $this->getCanReadPrice();
            $values = array();
            $scope = (int) Mage::app()->getStore()->getConfig(Mage_Core_Model_Store::XML_PATH_PRICE_SCOPE);
            foreach ($optionsArr as $option) {
                /* @var $option Mage_Catalog_Model_Product_Option */

                $this->setItemCount($option->getOptionId());

                $value = array();

                $value['id'] = $option->getOptionId();
                $value['item_count'] = $this->getItemCount();
                $value['option_id'] = $option->getOptionId();
                $value['title'] = $this->htmlEscape($option->getTitle());
                $value['type'] = $option->getType();
                $value['is_require'] = $option->getIsRequire();
                $value['sort_order'] = $option->getSortOrder();
                $value['can_edit_price'] = $this->getCanEditPrice();

                if ($this->getProduct()->getStoreId() != '0') {
                    $value['checkboxScopeTitle'] = $this->getCheckboxScopeHtml($option->getOptionId(), 'title', is_null($option->getStoreTitle()));
                    $value['scopeTitleDisabled'] = is_null($option->getStoreTitle()) ? 'disabled' : null;
                }

                if ($option->getGroupByType() == Mage_Catalog_Model_Product_Option::OPTION_GROUP_SELECT) {

//                    $valuesArr = array_reverse($option->getValues(), true);

                    $i = 0;
                    $itemCount = 0;
                    foreach ($option->getValues() as $_value) {
                        /* @var $_value Mage_Catalog_Model_Product_Option_Value */
                        // get value from db
                        
                        $special_from = $_value->getSpecialFrom();
                        /*
                        if(strlen((string)$_value->getSpecialFrom())>0){                            
                            $date = new DateTime();
                            $from = $date->createFromFormat('Y-m-d',$_value->getSpecialFrom());                            
                            $special_from = $from->format('d/m/Y');
                        }*/
                        
                        $special_to = $_value->getSpecialTo();
                        /*
                        if(strlen((string)$_value->getSpecialTo())>0){
                            $date = new DateTime();
                            $to = $date->createFromFormat('Y-m-d',$_value->getSpecialTo());                            
                            $special_to = $to->format('d/m/Y');
                        }*/
                        
                        $value['optionValues'][$i] = array(
                            'item_count' => max($itemCount, $_value->getOptionTypeId()),
                            'option_id' => $_value->getOptionId(),
                            'option_type_id' => $_value->getOptionTypeId(),
                            'title' => $this->htmlEscape($_value->getTitle()),
                            'price' => ($showPrice) ? $this->getPriceValue($_value->getPrice(), $_value->getPriceType()) : '',
                            'price_type' => ($showPrice) ? $_value->getPriceType() : 0,
                            'sku' => $this->htmlEscape($_value->getSku()),
                            'qty1'=> $_value->getQty1(),
                            'qty2'=> $_value->getQty2(),
                            'qty3'=> $_value->getQty3(),
                            'special_price'=> $_value->getSpecialPrice(),
                            'special_from'=> $special_from,
                            'special_to'=> $special_to,
                            'special_label'=> $_value->getSpecialLabel(),
                            'sort_order' => $_value->getSortOrder(),
                        );

                        if ($this->getProduct()->getStoreId() != '0') {
                            $value['optionValues'][$i]['checkboxScopeTitle'] = $this->getCheckboxScopeHtml(
                                    $_value->getOptionId(), 'title', is_null($_value->getStoreTitle()), $_value->getOptionTypeId());
                            $value['optionValues'][$i]['scopeTitleDisabled'] = is_null($_value->getStoreTitle()) ? 'disabled' : null;
                            if ($scope == Mage_Core_Model_Store::PRICE_SCOPE_WEBSITE) {
                                $value['optionValues'][$i]['checkboxScopePrice'] = $this->getCheckboxScopeHtml(
                                        $_value->getOptionId(), 'price', is_null($_value->getstorePrice()), $_value->getOptionTypeId());
                                $value['optionValues'][$i]['scopePriceDisabled'] = is_null($_value->getStorePrice()) ? 'disabled' : null;
                            }
                        }
                        $i++;
                    }
                } else {
                    //save value
                    $value['price'] = ($showPrice) ? $this->getPriceValue($option->getPrice(), $option->getPriceType()) : '';
                    $value['price_type'] = $option->getPriceType();
                    //$value['sku'] = $this->htmlEscape($option->getSku());
                    $value['qty1'] = $option->getQty1();
                    $value['qty2'] = $option->getQty2();
                    $value['qty3'] = $option->getQty3();
                    $value['special_price'] = $option->getSpecialPrice();
                    /*
                    $s_from = new DateTime();
                    $special_from = $s_from->createFromFormat('d/m/Y',$option->getSpecialFrom());
                    $s_to = new DateTime();
                    $special_to = $s_to->createFromFormat('d/m/Y',$option->getSpecialTo());
                    $value['special_from'] = $special_from->format('Y-m-d');                   
                    $value['special_to'] = $special_to->format('Y-m-d');                                         
                     */
                    $value['special_from'] = $option->getSpecialFrom();                   
                    $value['special_to'] = $option->getSpecialTo();                    
                    $value['special_label'] = $option->getSpecialLabel(); 
                    $value['max_characters'] = $option->getMaxCharacters();
                    $value['file_extension'] = $option->getFileExtension();
                    $value['image_size_x'] = $option->getImageSizeX();
                    $value['image_size_y'] = $option->getImageSizeY();
                    if ($this->getProduct()->getStoreId() != '0' &&
                            $scope == Mage_Core_Model_Store::PRICE_SCOPE_WEBSITE) {
                        $value['checkboxScopePrice'] = $this->getCheckboxScopeHtml($option->getOptionId(), 'price', is_null($option->getStorePrice()));
                        $value['scopePriceDisabled'] = is_null($option->getStorePrice()) ? 'disabled' : null;
                    }
                }
                $values[] = new Varien_Object($value);
            }
            $this->_values = $values;
        }

        return $this->_values;
    }
}
