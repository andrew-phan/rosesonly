<?php

abstract class Magazento_Pdfinvoice_Controller_Abstract extends Mage_Adminhtml_Controller_Action
{
    
    public function wysiwygAction() {
        $elementId = $this->getRequest()->getParam('element_id', md5(microtime()));
        $content = $this->getLayout()->createBlock('adminhtml/catalog_helper_form_wysiwyg_content', '', array(
                    'editor_element_id' => $elementId
                ));
        $this->getResponse()->setBody($content->toHtml());
    }
    
    protected function _initAction() {
        $this->loadLayout()->_setActiveMenu('system/pdfinvoice');
        return $this;
    }

    public function editAction() {
        if (Mage::helper('pdfinvoice')->versionUseAdminTitle()) {
            $this->_title($this->__('PDF '.$this->_type));
        }
        $this->_initAction()
                ->_addContent($this->getLayout()->createBlock('pdfinvoice/admin_'.$this->_type.'_edit')->setData('action', $this->getUrl('*/admin_'.$this->_type.'/save')))
                ->_addLeft($this->getLayout()->createBlock('pdfinvoice/admin_'.$this->_type.'_edit_tabs'))
                ->renderLayout();
    }    
    
    
    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {
            try {
                
                if (isset($_FILES['image']['name']) && $_FILES['image']['name'] != '') {
                    try {
                        $uploader = new Varien_File_Uploader('image');
                        $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                        $uploader->setAllowRenameFiles(false);
                        $uploader->setFilesDispersion(false);
                        $path = Mage::getBaseDir('media') . DS. 'magazento_pdfinvoice'.DS;

                        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                        $image_md5_name = md5($_FILES['image']['name']).'.'.$ext;
                        
                        $uploader->save($path, $image_md5_name);
                        $data['background_image'] = $image_md5_name;
                        
                    } catch (Exception $e) {
                        var_dump($e);
                    }
                } else {       
                    if (isset($data['image']['delete']) && $data['image']['delete'] == 1) {
                        $data['background_image'] = '';
                    }
                }         
//                var_dump($data['background_image']);
//                exit();
                Mage::getModel('pdfinvoice/data')->saveSettings($this->_type,$data);
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('pdfinvoice')->__('PDF '.$this->_type.' template was successfully saved'));
                $this->_redirect('*/*/edit');
                return;
            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // save data in session
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                // redirect to edit form
                $this->_redirect('*/*/edit');
                return;
            }
        }
        $this->_redirect('*/*/');
    }    
    
    
    
}
