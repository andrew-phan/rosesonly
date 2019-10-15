<?php

class Ant_Catalog_Block_Product_View_Options extends Mage_Catalog_Block_Product_View_Options {

    /**
     * Return html for control element
     *
     * @return string
     */
    public function getValuesHtml(Mage_Catalog_Model_Product_Option $_option) {
        //$_option = $this->getOption();
        $configValue = $this->getProduct()->getPreconfiguredValues()->getData('options/' . $_option->getId());
        $store = $this->getProduct()->getStore();

        if ($_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_DROP_DOWN
                || $_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_MULTIPLE) {
            $require = ($_option->getIsRequire()) ? ' required-entry' : '';
            $extraParams = '';
            $select = $this->getLayout()->createBlock('core/html_select')
                    ->setData(array(
                'id' => 'select_' . $_option->getId(),
                'class' => $require . ' product-custom-option'
                    ));
            if ($_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_DROP_DOWN) {
                $select->setName('options[' . $_option->getid() . ']')
                        ->addOption('', $this->__('-- Please Select --'));
            } else {
                $select->setName('options[' . $_option->getid() . '][]');
                $select->setClass('multiselect' . $require . ' product-custom-option');
            }
            foreach ($_option->getValues() as $_value) {
                $priceStr = $this->_formatPrice(array(
                    'is_percent' => ($_value->getPriceType() == 'percent'),
                    'pricing_value' => $_value->getPrice(($_value->getPriceType() == 'percent'))
                        ), false);
                $select->addOption(
                        $_value->getOptionTypeId(), $_value->getTitle() . ' ' . $priceStr . '', array('price' => $this->helper('core')->currencyByStore($_value->getPrice(true), $store, false))
                );
            }
            if ($_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_MULTIPLE) {
                $extraParams = ' multiple="multiple"';
            }
            if (!$this->getSkipJsReloadPrice()) {
                $extraParams .= ' onchange="opConfig.reloadPrice()"';
            }
            $select->setExtraParams($extraParams);

            if ($configValue) {
                $select->setValue($configValue);
            }

            return $select->getHtml();
        }

        if ($_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_RADIO
                || $_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_CHECKBOX
        ) {
            $selectHtml = '<ul id="options-' . $_option->getId() . '-list" class="options-list">';
            $require = ($_option->getIsRequire()) ? ' validate-one-required-by-name' : '';
            $arraySign = '';
            switch ($_option->getType()) {
                case Mage_Catalog_Model_Product_Option::OPTION_TYPE_RADIO:
                    $type = 'radio';
                    $class = 'radio';
                    if (!$_option->getIsRequire()) {
                        $selectHtml .= '<li><input type="radio" id="options_' . $_option->getId() . '" class="'
                                . $class . ' product-custom-option" name="options[' . $_option->getId() . ']"'
                                . ($this->getSkipJsReloadPrice() ? '' : ' onclick="opConfig.reloadPrice();"')
                                . ' value="" checked="checked" /><span class="label"><label for="options_'
                                . $_option->getId() . '">' . $this->__('None') . '</label></span></li>';
                    }
                    break;
                case Mage_Catalog_Model_Product_Option::OPTION_TYPE_CHECKBOX:
                    $type = 'checkbox';
                    $class = 'checkbox';
                    $arraySign = '[]';
                    break;
            }
            $count = 1;
            foreach ($_option->getValues() as $_value) {
                $count++;

                $priceStr = $this->_formatPrice(array(
                    'is_percent' => ($_value->getPriceType() == 'percent'),
                    'pricing_value' => $_value->getPrice($_value->getPriceType() == 'percent')
                        ));

                $htmlValue = $_value->getOptionTypeId();
                if ($arraySign) {
                    $checked = (is_array($configValue) && in_array($htmlValue, $configValue)) ? 'checked' : '';
                } else {
                    $checked = $configValue == $htmlValue ? 'checked' : '';
                }

                $name = preg_replace('/\s*/', '', $_value->getTitle());
                $class = strtolower(str_replace("/","",str_replace(" ","",$name)));
                
                if(strlen($_value->getSpecialFrom())>0)
                    $special_from = new DateTime($_value->getSpecialFrom());
                
                $show = false;
                $label ='';
                if(strlen($_value->getSpecialTo())>0)
                    $special_to = new DateTime($_value->getSpecialTo());
                if ($special_from != null || $special_to != null){
                    $now = new DateTime();
                    if (($special_from ==null || $now>= $special_from) &&($special_to ==null || $now <= $special_to)){
                        $show = true;
                        if(strlen($_value->getSpecialLabel())>0)
                            $label = '<span style="color:red;"> ('.$_value->getSpecialLabel().')</span>';
                    }
                }                
                
                $is_color = $_value->getQty1() + $_value->getQty2() + $_value->getQty3();
                if ($is_color != 0) {
                    $selectHtml .= '<li><input qty1="' . $_value->getQty1() . '" qty2="' . $_value->getQty2() . '" qty3="' . $_value->getQty3() . '" type="' . $type . '" class="ant_attribute ' . $class . ' ' . $require
                            . ' product-custom-option"'
                            . ($this->getSkipJsReloadPrice() ? 'onclick="updateQty(' . $_value->getQty1() . ',' . $_value->getQty2() . ',' . $_value->getQty3() . ')"' : ' onclick="opConfig.reloadPrice();updateQty(' . $_value->getQty1() . ',' . $_value->getQty2() . ',' . $_value->getQty3() . ')"')
                            . ' name="options[' . $_option->getId() . ']' . $arraySign . '" id="options_' . $_option->getId()
                            . '_' . $count . '" value="' . $htmlValue . '" ' . $checked . ' price="'
                            . $this->helper('core')->currencyByStore($_value->getPrice(true), $store, false) . '" />'
                            . '<span class="label"><label for="options_' . $_option->getId() . '_' . $count . '">'
                            . $_value->getTitle() . ' </label>' . $priceStr . '</span>'.$label ;
                } else {
                    $selectHtml .= '<li class="color"><div class="color-option p' . $class . '"></div>'
                            . '<input qty1="' . $_value->getQty1() . '" qty2="' . $_value->getQty2() . '" qty3="' . $_value->getQty3() . '" type="' . $type . '" class="ant_attribute ' . $class . ' ' . $require
                            . ' product-custom-option"'
                            . ($this->getSkipJsReloadPrice() ? 'onclick="updateQty(' . $_value->getQty1() . ',' . $_value->getQty2() . ',' . $_value->getQty3() . ')"' : ' onclick="opConfig.reloadPrice();updateQty(' . $_value->getQty1() . ',' . $_value->getQty2() . ',' . $_value->getQty3() . ')"')
                            . ' name="options[' . $_option->getId() . ']' . $arraySign . '" id="options_' . $_option->getId()
                            . '_' . $count . '" value="' . $htmlValue . '" ' . $checked . ' price="'
                            . $this->helper('core')->currencyByStore($_value->getPrice(true), $store, false) . '" />'
                            . '<div><label for="options_' . $_option->getId() . '_' . $count . '">'
                            . $_value->getTitle() . ' </label>' . $priceStr . '</div>';
                }
                if ($_option->getIsRequire()) {
                    $selectHtml .= '<script type="text/javascript">' . '$(\'options_' . $_option->getId() . '_'
                            . $count . '\').advaiceContainer = \'options-' . $_option->getId() . '-container\';'
                            . '$(\'options_' . $_option->getId() . '_' . $count
                            . '\').callbackFunction = \'validateOptionsCallback\';' . '</script>';
                }
                $selectHtml .= '</li>';
            }
            $selectHtml .= '</ul>';

            return $selectHtml;
        }
    }

    /**
     * Return formated price
     *
     * @param array $value
     * @return string
     */
    protected function _formatPrice($value, $flag = true) {
        if ($value['pricing_value'] == 0) {
            return '';
        }

        $taxHelper = Mage::helper('tax');
        $store = $this->getProduct()->getStore();

        $sign = '+';
        if ($value['pricing_value'] < 0) {
            $sign = '-';
            $value['pricing_value'] = 0 - $value['pricing_value'];
        }

        $priceStr = $sign;

        $_priceInclTax = $this->getPrice($value['pricing_value'], true);
        $_priceExclTax = $this->getPrice($value['pricing_value']);
        /* if ($taxHelper->displayPriceIncludingTax()) {
          $priceStr .= $this->helper('core')->currencyByStore($_priceInclTax, $store, true, $flag);
          } elseif ($taxHelper->displayPriceExcludingTax()) {
          $priceStr .= $this->helper('core')->currencyByStore($_priceExclTax, $store, true, $flag);
          } elseif ($taxHelper->displayBothPrices()) {
          $priceStr .= $this->helper('core')->currencyByStore($_priceExclTax, $store, true, $flag);
          if ($_priceInclTax != $_priceExclTax) {
          $priceStr .= ' (' . $sign . $this->helper('core')
          ->currencyByStore($_priceInclTax, $store, true, $flag) . ' ' . $this->__('Incl. Tax') . ')';
          }
          } */
        $priceStr .= $this->helper('core')->currencyByStore($_priceExclTax, $store, true, $flag);
        if ($flag) {
            $priceStr = '<span class="price-notice">' . $priceStr . '</span>';
        }

        return $priceStr;
    }

    /**
     * Get price with including/excluding tax
     *
     * @param decimal $price
     * @param bool $includingTax
     * @return decimal
     */
    public function getPrice($price, $includingTax = null) {
        if (!is_null($includingTax)) {
            $price = Mage::helper('tax')->getPrice($this->getProduct(), $price, true);
        } else {
            $price = Mage::helper('tax')->getPrice($this->getProduct(), $price);
        }
        return $price;
    }

}