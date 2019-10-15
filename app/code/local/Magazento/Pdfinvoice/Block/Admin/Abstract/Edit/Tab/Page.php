<?php
/*
 *  Created on Aug 30, 2012
 *  Author Ivan Proskuryakov - volgodark@gmail.com - Magazento.com
 *  Copyright Proskuryakov Ivan. Magazento.com © 2011. All Rights Reserved.
 *  Single Use, Limited Licence and Single Use No Resale Licence ["Single Use"]
 */
?>
<?php

class Magazento_Pdfinvoice_Block_Admin_Abstract_Edit_Tab_Page extends Mage_Adminhtml_Block_Widget_Form {


    protected function _prepareForm() {
        
        $node = Mage::helper('pdfinvoice')->getPdfTypeByController();
        $data = Mage::getSingleton('pdfinvoice/data')->getSettings($node);
        $path = Mage::getBaseUrl('media') . DS. 'magazento_pdfinvoice'.DS;
//        var_dump($data[$node]['main_content']);
        
        $form = new Varien_Data_Form(array('id' => 'edit_form_item', 'action' => $this->getData('action'), 'method' => 'post'));
        $form->setHtmlIdPrefix('item_');
        $fieldset = $form->addFieldset('base_fieldset', array('legend' => Mage::helper('pdfinvoice')->__('General Information'), 'class' => 'fieldset-wide'));

        if (Mage::helper('pdfinvoice')->versionUseWysiwig()) {
            $wysiwygConfig = Mage::getSingleton('pdfinvoice/wysiwyg_config')->getConfig();
        } else {
            $wysiwygConfig = '';
        }

        $fieldset->addField('image', 'image', array(
          'label'     => Mage::helper('pdfinvoice')->__('Background image'),
          'required'  => true,
          'name'      => 'image',
          'value' => $path.$data[$node]['background_image'],  
            
        )); 
//        var_dump($path.$data[$node]['background_image']);
        
        $fieldset->addField('page_css', 'editor', array(
            'name' => 'page_css',
            'label' => Mage::helper('pdfinvoice')->__('Css'),
            'title' => Mage::helper('pdfinvoice')->__('Css'),
            'style' => 'height:26em',
            'value' => $data[$node]['page_css'],
            'required' => false,
        ));        
        
        
//        $fieldset->addField('pdf_layout', 'select', array(
//            'label' => Mage::helper('pdfproduct')->__('PDF layout'),
//            'title' => Mage::helper('pdfproduct')->__('PDF layout'),
//            'name' => 'pdf_layout',
//            'required' => true,
//            'value' => $data[$node]['pdf_layout'],
//            'options' => array(
//                'L' => Mage::helper('pdfproduct')->__('Lanscape'),
//                'P' => Mage::helper('pdfproduct')->__('Portrait'),
//            ),
//        ));
        
        
//        print_r($model->getData());
//        exit();
//        $form->setUseContainer(true);
//        $form->setValues($model->getData());
        
        
        
        $this->setForm($form);

        return parent::_prepareForm();
    }

}
