<?php
/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@mageworx.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 * or send an email to sales@mageworx.com
 *
 * @category   MageWorx
 * @package    MageWorx_Adminhtml
 * @copyright  Copyright (c) 2009 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * MageWorx Adminhtml extension
 *
 * @category   MageWorx
 * @package    MageWorx_Adminhtml
 * @author     MageWorx Dev Team <dev@mageworx.com>
 */

class MageWorx_Adminhtml_XsitemapController extends  Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('catalog/xsitemap_generate')
            ->_addBreadcrumb(Mage::helper('catalog')->__('Catalog'), Mage::helper('catalog')->__('Catalog'))
            ->_addBreadcrumb(Mage::helper('xsitemap')->__('Google Sitemap (Extended)'), Mage::helper('xsitemap')->__('Google Sitemap (Extended)'))
        ;
        return $this;
    }

    public function indexAction()
    {
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('mageworx/xsitemap'))
            ->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('sitemap_id');
        $model = Mage::getModel('xsitemap/sitemap');

        if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xsitemap')->__('This sitemap no longer exists'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (! empty($data)) {
            $model->setData($data);
        }

        Mage::register('sitemap_sitemap', $model);

        $this->_initAction()
            ->_addBreadcrumb($id ? Mage::helper('xsitemap')->__('Edit Sitemap') : Mage::helper('xsitemap')->__('New Sitemap'), $id ? Mage::helper('xsitemap')->__('Edit Sitemap') : Mage::helper('xsitemap')->__('New Sitemap'))
            ->_addContent($this->getLayout()->createBlock('mageworx/xsitemap_edit'))
            ->renderLayout();
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('xsitemap/sitemap');

            if ($this->getRequest()->getParam('sitemap_id')) {
                $model ->load($this->getRequest()->getParam('sitemap_id'));

                if ($model->getSitemapFilename() && file_exists($model->getPreparedFilename())){
                    unlink($model->getPreparedFilename());
                }
            }


            $model->setData($data);

            try {
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('xsitemap')->__('Sitemap was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('sitemap_id' => $model->getId()));
                    return;
                }
                if ($this->getRequest()->getParam('generate')) {
                    $this->getRequest()->setParam('sitemap_id', $model->getId());
                    $this->_forward('generate');
                    return;
                }
                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('sitemap_id' => $this->getRequest()->getParam('sitemap_id')));
                return;
            }
        }
        $this->_redirect('*/*/');

    }

    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('sitemap_id')) {
            try {
                $model = Mage::getModel('xsitemap/sitemap');
                $model->setId($id);

                /* @var $sitemap MageWorx_XSitemap_Model_Sitemap */
                $model->load($id);
                if ($model->getSitemapFilename() && file_exists($model->getPreparedFilename())){
                    unlink($model->getPreparedFilename());
                }
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('xsitemap')->__('Sitemap was successfully deleted'));
                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('sitemap_id' => $id));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xsitemap')->__('Unable to find a sitemap to delete'));
        $this->_redirect('*/*/');
    }

    public function generateAction() {
//        $sitemapId = intval($this->getRequest()->getParam('sitemap_id'));        
//        $sitemap = Mage::getModel('xsitemap/sitemap')->load($sitemapId);        
//        $sitemap->generateXml();
//        exit;
        $this->loadLayout();
        $this->renderLayout();
    }    
    
    public function runGenerateAction() {
        
        $sitemapId = intval($this->getRequest()->getParam('sitemap_id'));
        if (!$sitemapId) return false;
        
        $sitemap = Mage::getModel('xsitemap/sitemap')->load($sitemapId);        
        if (!$sitemap->getId()) return false;
                
        $action = $this->getRequest()->getParam('action', '');
        if (!$action) return false;
        
        
        $sitemap->_sitemapInc = intval($this->getRequest()->getParam('sitemap_inc', 1));
        $sitemap->_linkInc = intval($this->getRequest()->getParam('link_inc', 0));
        $sitemap->_currentInc = intval($this->getRequest()->getParam('current_inc', 0));
        
        
        $sitemap->generateXml($action);
        
        $result = array();
        // 'category', 'product', 'tag', 'cms', 'additional_links', 'sitemap_finish'
        switch ($action) {
            case 'category': 
                $result['text'] = $this->__('Generate categories (100%)...');
                $action = 'product'; 
                break;
            case 'product':                                                
                if ($sitemap->_currentInc>=$sitemap->_totalProducts) {                                                                
                    $result['text'] = $this->__('Generate products (100%)...');
                    $sitemap->_currentInc = 0;
                    $action = 'tag';
                } else {
                    $result['text'] = $this->__('Generate products, total %1$s, processed %2$s products (%3$s%%)...', $sitemap->_totalProducts, $sitemap->_currentInc, round($sitemap->_currentInc*100/$sitemap->_totalProducts, 2));
                }                
                break;
            case 'tag': 
                $result['text'] = $this->__('Generate tags (100%)...');
                $action = 'cms'; 
                break;
            case 'cms': 
                $result['text'] = $this->__('Generate CMS pages (100%)...');
                $action = 'additional_links';
                break;
            case 'additional_links': 
                $result['text'] = $this->__('Generate additional links (100%)...');
                $action = 'sitemap_finish'; 
                break;            
            case 'sitemap_finish': 
                $result['text'] = $this->__('Generate sitemap index (100%)...');
                $result['stop'] = 1;
                $this->_getSession()->addSuccess(Mage::helper('xsitemap')->__('Sitemap "%s" has been successfully generated', $sitemap->getSitemapFilename()));
                $action = ''; 
                break;
        }
        
        $result['url'] = $this->getUrl('*/*/runGenerate/', array('sitemap_id'=>$sitemapId, 'action'=>$action, 'sitemap_inc'=>$sitemap->_sitemapInc, 'link_inc'=>$sitemap->_linkInc, 'current_inc'=>$sitemap->_currentInc));
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
    
    

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/xsitemap');
    }

}