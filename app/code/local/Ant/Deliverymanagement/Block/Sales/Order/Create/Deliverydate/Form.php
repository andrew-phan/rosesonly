<?php

class Ant_Deliverymanagement_Block_Sales_Order_Create_Deliverydate_Form extends Mage_Adminhtml_Block_Widget_Form
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_create_deliverydate_form_deliverydate');
    }

//    protected function _prepareForm() {
//        $form = new Varien_Data_Form();
//        $fieldset = $form->addFieldset('main', array('no_container'=>true));
//
//        $fieldset->addField('type','hidden',
//            array(
//                'name' =>  $this->_getFieldName('type'),
//            )
//        );
//
//        $form->setHtmlIdPrefix($this->_getFieldIdPrefix());
//
//        if ($this->getEntityType() == 'item') {
//            $this->_prepareHiddenFields($fieldset);
//        } else {
//            $this->_prepareVisibleFields($fieldset);
//        }
//
//        // Set default sender and recipient from billing and shipping adresses
//        if(!$this->getMessage()->getSender()) {
//            $this->getMessage()->setSender($this->getDefaultSender());
//        }
//
//        if(!$this->getMessage()->getRecipient()) {
//            $this->getMessage()->setRecipient($this->getDefaultRecipient());
//        }
//
//        $this->getMessage()->setType($this->getEntityType());
//
//        // Overriden default data with edited when block reloads througth Ajax
//        $this->_applyPostData();
//
//        $form->setValues($this->getMessage()->getData());
//
//        $this->setForm($form);
//        return $this;
//    }

}