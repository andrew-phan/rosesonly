<?php

class Ant_Notices_Block_Adminhtml_Notices_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {

        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset("notices_form", array("legend" => Mage::helper("notices")->__("Item information")));

        $fieldset->addField("title", "text", array(
            "label" => Mage::helper("notices")->__("Title"),
            "class" => "required-entry",
            "required" => true,
            "name" => "title",
        ));

        $fieldset->addField('description', 'editor', array(
            'name' => 'description',
            'label' => Mage::helper('notices')->__('Description'),
            'title' => Mage::helper('notices')->__('Description'),
            'style' => 'width:700px; height:500px;',
            'wysiwyg' => false,
            'required' => true,
        ));

        if (Mage::getSingleton("adminhtml/session")->getNoticesData()) {
            $form->setValues(Mage::getSingleton("adminhtml/session")->getNoticesData());
            Mage::getSingleton("adminhtml/session")->setNoticesData(null);
        } elseif (Mage::registry("notices_data")) {
            $form->setValues(Mage::registry("notices_data")->getData());
        }
        return parent::_prepareForm();
    }

}
