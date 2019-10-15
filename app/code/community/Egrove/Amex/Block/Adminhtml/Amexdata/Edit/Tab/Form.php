<?php

class Egrove_Amex_Block_Adminhtml_Amexdata_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('amexdata_form', array('legend'=>Mage::helper('amex')->__('Response Log')));
     
      $fieldset->addField('amount', 'text', array(  
                'name' => 'amount',  
                'label' => 'Amount'
                ));
      $fieldset->addField('order_id', 'text', array(  
                'name' => 'order_id',  
                'label' => 'Order Id',
                ));
      $fieldset->addField('authorized_id', 'text', array(  
                'name' => 'authorized_id',  
                'label' => 'Authorized Id',
                ));
       $fieldset->addField('message', 'text', array(  
                'name' => 'message',  
                'label' => 'Authorized Message'
                ));
      $fieldset->addField('transation_no', 'text', array(  
                'name' => 'transation_no',  
                'label' => 'Transation No',
                ));
      $fieldset->addField('capture_message', 'text', array(  
                'name' => 'capture_message',  
                'label' => 'Capture Message',
                ));
      $fieldset->addField('capture_tno', 'text', array(  
                'name' => 'capture_tno',  
                'label' => 'Capture Transation Number',
                )); 
      $fieldset->addField('capture_rno', 'text', array(  
                'name' => 'capture_rno',  
                'label' => 'Capture Receipt Number',
                ));
      $fieldset->addField('capture_amount', 'text', array(  
                'name' => 'capture_amount',  
                'label' => 'Capture Amount',
                )); 
      
      
      if ( Mage::getSingleton('adminhtml/session')->getAmexData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getAmexData());
          Mage::getSingleton('adminhtml/session')->setAmexData(null);
      } elseif ( Mage::registry('amexdata_data') ) {
          $form->setValues(Mage::registry('amexdata_data')->getData());
      }
      return parent::_prepareForm();
  }
}