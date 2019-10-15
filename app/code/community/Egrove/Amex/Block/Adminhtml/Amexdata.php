<?php
class Egrove_Amex_Block_Adminhtml_Amexdata extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_amexdata';
    $this->_blockGroup = 'amex';
    $this->_headerText = Mage::helper('amex')->__('Amex Response Log');
    parent::__construct();
    $this->removeButton('add');
  }
}