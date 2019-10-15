<?php

class Egrove_Amex_Block_Adminhtml_Amexdata_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('amex_tabs');
      $this->setDestElementId('edit_form');
      
  }
  
  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('amex')->__('Amex Response Log'),
          'title'     => Mage::helper('amex')->__('Amex Response Log'),
          'content'   => $this->getLayout()->createBlock('amex/adminhtml_amexdata_edit_tab_form')->toHtml(),
      ));
     
      //removed
      return parent::_beforeToHtml();
  }
}