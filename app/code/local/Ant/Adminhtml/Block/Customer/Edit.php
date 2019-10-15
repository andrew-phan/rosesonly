<?php

class Ant_Adminhtml_Block_Customer_Edit extends Mage_Adminhtml_Block_Customer_Edit
{
    protected function _prepareLayout()
    {
        if (!Mage::registry('current_customer')->isReadonly()) {
        $this->_addButton('save_and_create_order', array(
                'label'     => Mage::helper('customer')->__('Save and Create New Order'),
                //'onclick'   => 'saveAndContinueEdit(\''.$this->_getSaveAndCreateUrl().'\')',
                'onclick'   => 'editForm.submit(\''.$this->_getSaveAndCreateUrl().'\')',
                'class'     => 'save'
            ), 10);
        }
        $this->removeButton('order');
        return parent::_prepareLayout();
    }
    
    protected function _getSaveAndCreateUrl()
    {
        return $this->getUrl('*/*/save', array(
            '_current'  => true,
            'create'       => 'create'
        ));
    }
}