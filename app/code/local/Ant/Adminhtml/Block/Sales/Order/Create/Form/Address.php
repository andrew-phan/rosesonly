<?php


/**
 * Order create address form
 *
 * @category    Ant
 * @package     Ant_Adminhtml
 * @author      Hoang
 */
class Ant_Adminhtml_Block_Sales_Order_Create_Form_Address
    extends Mage_Adminhtml_Block_Sales_Order_Create_Form_Address
{    
    /**
     * Prepare Form and add elements to form
     *
     * @return Mage_Adminhtml_Block_Sales_Order_Create_Form_Address
     */
    protected function _prepareForm()
    {
        $fieldset = $this->_form->addFieldset('main', array(
            'no_container' => true
        ));

        /* @var $addressModel Mage_Customer_Model_Address */
        $addressModel = Mage::getModel('customer/address');

        $addressForm = $this->_getAddressForm()
            ->setEntity($addressModel);

        $attributes = $addressForm->getAttributes();
        if(isset($attributes['street'])) {
            Mage::helper('adminhtml/addresses')
                ->processStreetAttribute($attributes['street']);
        }
        $this->_addAttributesToForm($attributes, $fieldset);

        $prefixElement = $this->_form->getElement('prefix');
        if ($prefixElement) {
            $prefixOptions = $this->helper('customer')->getNamePrefixOptions($this->getStore());
            if (!empty($prefixOptions)) {
                $fieldset->removeField($prefixElement->getId());
                $prefixField = $fieldset->addField($prefixElement->getId(),
                    'select',
                    $prefixElement->getData(),
                    '^'
                );
                $prefixField->setValues($prefixOptions);
                if ($this->getAddressId()) {
                    $prefixField->addElementValues($this->getAddress()->getPrefix());
                }
            }
        }

        $suffixElement = $this->_form->getElement('suffix');
        if ($suffixElement) {
            $suffixOptions = $this->helper('customer')->getNameSuffixOptions($this->getStore());
            if (!empty($suffixOptions)) {
                $fieldset->removeField($suffixElement->getId());
                $suffixField = $fieldset->addField($suffixElement->getId(),
                    'select',
                    $suffixElement->getData(),
                    $this->_form->getElement('lastname')->getId()
                );
                $suffixField->setValues($suffixOptions);
                if ($this->getAddressId()) {
                    $suffixField->addElementValues($this->getAddress()->getSuffix());
                }
            }
        }


        $regionElement = $this->_form->getElement('region_id');
        if ($regionElement) {
            $regionElement->setNoDisplay(true);
        }

        $this->_form->setValues($this->getFormValues());

        if ($this->_form->getElement('country_id')->getValue()) {
            $countryId = $this->_form->getElement('country_id')->getValue();
            $this->_form->getElement('country_id')->setValue(null);
            foreach ($this->_form->getElement('country_id')->getValues() as $country) {
                if ($country['value'] == $countryId) {
                    $this->_form->getElement('country_id')->setValue($countryId);
                }
            }
        }
        if (is_null($this->_form->getElement('country_id')->getValue())) {
            $this->_form->getElement('country_id')->setValue(
                Mage::helper('core')->getDefaultCountry($this->getStore())
            );
        }

        // Set custom renderer for VAT field if needed
        $vatIdElement = $this->_form->getElement('vat_id');
        if ($vatIdElement && $this->getDisplayVatValidationButton() !== false) {
            $vatIdElement->setRenderer(
                $this->getLayout()->createBlock('adminhtml/customer_sales_order_address_form_renderer_vat')
                    ->setJsVariablePrefix($this->getJsVariablePrefix())
            );
        }
        //Phuoc's code
        /*
        $taxElement = $this->_form->getElement('vat_id');
        $fieldset->removeField($taxElement->getId());
        
        $telephoneElement = $this->_form->getElement('telephone');
        $telephoneElement->setRequired(true);
        
        $addressElement = $this->_form->getElement('street');
        $addressElement->setRequired(true);
        
        $firstnameElement = $this->_form->getElement('firstname');
        $firstnameElement->setRequired(true);
        
        $lastnameElement = $this->_form->getElement('lastname');
        $lastnameElement->setRequired(true);
        
        $cityElement = $this->_form->getElement('city');
        $cityElement->setRequired(true);
        
        //Hoang Code
        if ($this->getCustomer()->getData('prefix')!='')
            $this->_form->getElement('prefix')->setValue($this->getCustomer()->getData('prefix'));
        
        if ($this->getCustomer()->getData('firstname')!='')
            $this->_form->getElement('firstname')->setValue($this->getCustomer()->getData('firstname'));
        
        if ($this->getCustomer()->getData('middlename')!='')
            $this->_form->getElement('middlename')->setValue($this->getCustomer()->getData('middlename'));
        
        if ($this->getCustomer()->getData('lastname')!='')
            $this->_form->getElement('lastname')->setValue($this->getCustomer()->getData('lastname'));
        
        if ($this->getCustomer()->getData('suffix')!='')
            $this->_form->getElement('suffix')->setValue($this->getCustomer()->getData('suffix'));
        // end code
        */
        return $this;
    }

}
