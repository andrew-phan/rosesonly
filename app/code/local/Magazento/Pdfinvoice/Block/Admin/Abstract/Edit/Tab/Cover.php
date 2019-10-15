<?php
/*
 *  Created on Aug 30, 2012
 *  Author Ivan Proskuryakov - volgodark@gmail.com - Magazento.com
 *  Copyright Proskuryakov Ivan. Magazento.com © 2011. All Rights Reserved.
 *  Single Use, Limited Licence and Single Use No Resale Licence ["Single Use"]
 */
?>
<?php
class Magazento_Pdfinvoice_Block_Admin_Abstract_Edit_Tab_Cover extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
        $node = Mage::helper('pdfinvoice')->getPdfTypeByController();
        $data = Mage::getSingleton('pdfinvoice/data')->getSettings($node);
        
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('additional_form', array('legend' => Mage::helper('pdfinvoice')->__('Additional Information'), 'class' => 'fieldset-wide'));

        
        $fieldset->addField('cover_page_active', 'select', array(
            'label' => Mage::helper('pdfinvoice')->__('Status'),
            'title' => Mage::helper('pdfinvoice')->__('Status'),
            'name' => 'cover_page_active',
            'value' => $data[$node]['cover_page_active'],
            'required' => true,
            'options' => array(
                '1' => Mage::helper('pdfinvoice')->__('Enabled'),
                '0' => Mage::helper('pdfinvoice')->__('Disabled'),
            ),
        ));

        if (Mage::helper('pdfinvoice')->versionUseWysiwig()) {
            $wysiwygConfig = Mage::getSingleton('pdfinvoice/wysiwyg_config')->getConfig();
        } else {
            $wysiwygConfig = '';
        }

        $fieldset->addField('cover_content_header', 'editor', array(
            'name' => 'cover_content_header',
            'label' => Mage::helper('pdfinvoice')->__('Header'),
            'title' => Mage::helper('pdfinvoice')->__('Header'),
            'style' => 'height:6em',
            'config' => $wysiwygConfig,
            'value' => $data[$node]['cover_content_header'],
            'required' => false,
        ));
        
        $fieldset->addField('cover_content', 'editor', array(
            'name' => 'cover_content',
            'label' => Mage::helper('pdfinvoice')->__('Content'),
            'title' => Mage::helper('pdfinvoice')->__('Content'),
            'style' => 'height:36em',
            'config' => $wysiwygConfig,
            'value' => $data[$node]['cover_content'],
            'required' => false,
        ));
        
        $fieldset->addField('cover_content_footer', 'editor', array(
            'name' => 'cover_content_footer',
            'label' => Mage::helper('pdfinvoice')->__('Footer'),
            'title' => Mage::helper('pdfinvoice')->__('Footer'),
            'style' => 'height:6em',
            'config' => $wysiwygConfig,
            'value' => $data[$node]['cover_content_footer'],
            'required' => false,
        ));        

        $fieldset->addField('cover_page_layout', 'select', array(
            'label' => Mage::helper('pdfinvoice')->__('Layout'),
            'title' => Mage::helper('pdfinvoice')->__('Layout'),
            'name' => 'cover_page_layout',
            'value' => $data[$node]['cover_page_layout'],
            'required' => true,
            'options' => array(
                'L' => Mage::helper('pdfinvoice')->__('Landscape'),
                'P' => Mage::helper('pdfinvoice')->__('Portrait'),
            ),
        ));
        

        $fieldset->addField('cover_page_margin_top', 'text', array(
            'name' => 'cover_page_margin_top',
            'label' => Mage::helper('pdfinvoice')->__('Page margin top'),
            'title' => Mage::helper('pdfinvoice')->__('Page margin top'),
            'value' => $data[$node]['cover_page_margin_top'],
            'required' => true,
        ));
        
        $fieldset->addField('cover_page_margin_right', 'text', array(
            'name' => 'cover_page_margin_right',
            'label' => Mage::helper('pdfinvoice')->__('Page margin right'),
            'title' => Mage::helper('pdfinvoice')->__('Page margin right'),
            'value' => $data[$node]['cover_page_margin_right'],
            'required' => true,
        ));
        
        $fieldset->addField('cover_page_margin_bottom', 'text', array(
            'name' => 'cover_page_margin_bottom',
            'label' => Mage::helper('pdfinvoice')->__('Page margin bottom'),
            'title' => Mage::helper('pdfinvoice')->__('Page margin bottom'),
            'value' => $data[$node]['cover_page_margin_bottom'],
            'required' => true,
        ));
        
        $fieldset->addField('cover_page_margin_left', 'text', array(
            'name' => 'cover_page_margin_left',
            'label' => Mage::helper('pdfinvoice')->__('Page margin left'),
            'title' => Mage::helper('pdfinvoice')->__('Page margin left'),
            'value' => $data[$node]['cover_page_margin_left'],
            'required' => true,
        ));
        
        $fieldset->addField('cover_page_margin_header', 'text', array(
            'name' => 'cover_page_margin_header',
            'label' => Mage::helper('pdfinvoice')->__('Page margin header'),
            'title' => Mage::helper('pdfinvoice')->__('Page margin header'),
            'value' => $data[$node]['cover_page_margin_header'],
            'required' => true,
        ));
        
        $fieldset->addField('cover_page_margin_footer', 'text', array(
            'name' => 'cover_page_margin_footer',
            'label' => Mage::helper('pdfinvoice')->__('Page margin footer'),
            'title' => Mage::helper('pdfinvoice')->__('Page margin footer'),
            'value' => $data[$node]['cover_page_margin_footer'],
            'required' => true,
        ));        
        
        
//        $form->setUseContainer(true);
//        $form->setValues($model->getData());
        $this->setForm($form);

      return parent::_prepareForm();
  }
}