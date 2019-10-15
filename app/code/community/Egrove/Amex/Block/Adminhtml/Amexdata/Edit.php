<?php

class Egrove_Amex_Block_Adminhtml_Amexdata_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'amex';
        $this->_controller = 'adminhtml_amexdata';
        
        $this->_updateButton('delete', 'label', Mage::helper('amex')->__('Delete Log'));
	$this->removeButton('save');
	
    }

    public function getHeaderText()
    {
      return Mage::helper('amex')->__('');
    }
}