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
 * @package    MageWorx_SeoSuite
 * @copyright  Copyright (c) 2010 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * SEO Suite extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoSuite
 * @author     MageWorx Dev Team <dev@mageworx.com>
 */
class MageWorx_SeoSuite_Block_Page_Html_Head extends MageWorx_SeoSuite_Block_Page_Html_Head_Abstract {

    public function getCssJsHtml() {
        if ($this->setCanonicalUrl() || empty($this->_data['canonical_url'])) {
            return parent::getCssJsHtml();
        }
        $html = '<link rel="canonical" href="' . $this->_data['canonical_url'] . '" />' . "\n";
        $html .= parent::getCssJsHtml();
        return $html;
    }

    public function setCanonicalUrl() {
        if (!Mage::getStoreConfig('mageworx_seo/seosuite/enabled')) {
            return;
        }
        
        if (strpos($this->getAction()->getRequest()->getRequestString(), '/l/') !== FALSE &&
               !Mage::getStoreConfigFlag('mageworx_seo/seosuite/enable_canonical_tag_for_layered_navigation') ) {
            return;
        }
        

        $canonicalUrl = null;

        $productActions = array(
            'catalog_product_view',
            'review_product_list',
            'review_product_view',
            'productquestions_show_index',
        );

        if (empty($this->_data['canonical_url'])) {
            if (in_array($this->getAction()->getFullActionName(), array_filter(preg_split('/\r?\n/', Mage::getStoreConfig('mageworx_seo/seosuite/ignore_pages'))))) {
                return;
            } elseif (in_array($this->getAction()->getFullActionName(), $productActions)) {
                $useCategories = Mage::getStoreConfigFlag('catalog/seo/product_use_categories');
                if ($product = Mage::registry('current_product')) {
                    if ($canonicalUrl = $product->getCanonicalUrl()) {
                        $urlRewrite = Mage::getModel('core/url_rewrite')->loadByIdPath($canonicalUrl);
                        $canonicalUrl = Mage::getUrl('') . $urlRewrite->getRequestPath();
                    } else {
                        $productCanonicalUrl = Mage::getStoreConfig('mageworx_seo/seosuite/product_canonical_url');
                        $collection = Mage::getResourceModel('seosuite/core_url_rewrite_collection')
                                ->filterAllByProductId($product->getId(), $productCanonicalUrl)
                                ->addStoreFilter(Mage::app()->getStore()->getId(), false);
                        if ($urlRewrite = $collection->getFirstItem()) {
                            $canonicalUrl = Mage::getUrl('') . $urlRewrite->getRequestPath();
                        }

                        if (!$canonicalUrl) {
                            $canonicalUrl = $product->getProductUrl(false);
                            if (!$canonicalUrl || $productCanonicalUrl == 0) {
                                $product->setDoNotUseCategoryId(!$useCategories);
                                $canonicalUrl = $product->getProductUrl(false);
                            }
                        }
                    }
                }
            } else {
                $url = Mage::helper('core/url')->getCurrentUrl();
                $parsedUrl = parse_url($url);
                extract($parsedUrl);
                $canonicalUrl = $scheme . '://' . $host . (isset($port) && '80' != $port ? ':' . $port : '') . $path;
            }
            if ($canonicalUrl) {
                if (Mage::getStoreConfig('mageworx_seo/seosuite/trailing_slash')) {
                    if ('/' != substr($canonicalUrl, -1) && !in_array(substr(strrchr($canonicalUrl, '.'), 1), array('rss', 'html', 'htm', 'xml', 'php'))) {
                        $canonicalUrl .= '/';
                    }
                }
            }
            if ($crossDomain = Mage::getStoreConfig('mageworx_seo/seosuite/cross_domain')) {
                $url = Mage::app()->getStore($crossDomain)->getBaseUrl();
                $canonicalUrl = str_replace(Mage::getUrl(), $url, $canonicalUrl);
            }
            $this->_data['canonical_url'] = $canonicalUrl;
        }
        if (method_exists($this, 'addLinkRel') && !empty($this->_data['canonical_url'])) {
            $this->addLinkRel('canonical', $this->_data['canonical_url']);
            return true;
        }
    }

    public function getRobots() {
        $noindexPatterns = explode(',', Mage::getStoreConfig('mageworx_seo/seosuite/noindex_pages'));
        foreach ($noindexPatterns as $pattern) {
            if (preg_match('/' . $pattern . '/', $this->getAction()->getFullActionName())) {
                $this->_data['robots'] = 'NOINDEX, FOLLOW';
                break;
            }
        }
        $noindexPatterns = array_filter(preg_split('/\r?\n/', Mage::getStoreConfig('mageworx_seo/seosuite/noindex_pages_user')));
        foreach ($noindexPatterns as $pattern) {
            $pattern = str_replace('?', '\?', $pattern);
            $pattern = str_replace('*', '.*?', $pattern);
            if (preg_match('%' . $pattern . '%', $this->getAction()->getFullActionName()) ||
                    preg_match('%' . $pattern . '%', $this->getAction()->getRequest()->getRequestString()) ||
                    preg_match('%' . $pattern . '%', $this->getAction()->getRequest()->getRequestUri())
            ) {
                $this->_data['robots'] = 'NOINDEX, FOLLOW';
                break;
            }
        }
        if (empty($this->_data['robots'])) {
            $this->_data['robots'] = Mage::getStoreConfig('design/head/default_robots');
        }

        return $this->_data['robots'];
    }

    public function getTitle() {
        if ($this->getAction()->getFullActionName() == 'xsitemap_index_index') {
            $this->_data['title'] = Mage::getStoreConfig('mageworx_seo/xsitemap/sitemap_meta_title');
        } else if ($this->_product = Mage::registry('current_product')) {
            $title = '';
            if (!$this->_product->getMetaTitle()) {
                $titleTemplate = Mage::getStoreConfig('mageworx_seo/seosuite/product_meta_title');
                $template = Mage::getModel('seosuite/catalog_product_template_title');
                $template->setTemplate($titleTemplate)
                        ->setProduct($this->_product);
                $title = $template->process();
            }
            if ($title) {
                $this->_data['title'] = $title;
            }
        }

        $this->_convertLayerMeta();

        if (empty($this->_data['title'])) {
            $this->_data['title'] = $this->getDefaultTitle();
        }

        return trim(htmlspecialchars(html_entity_decode($this->_data['title'], ENT_QUOTES, 'UTF-8')));
    }

    public function getDescription() {
                
        
        $oldDescription = empty($this->_data['description']) ? Mage::getStoreConfig('design/head/default_description') : $this->_data['description'];                        
        $this->_data['description'] = '';

        if ($this->getAction()->getFullActionName() == 'xsitemap_index_index') {
            $this->_data['description'] = Mage::getStoreConfig('mageworx_seo/xsitemap/sitemap_meta_desc');
        } elseif ($this->_product = Mage::registry('current_product')) {
            if ($this->_product->getMetaDescription()) {
                $this->_data['description'] = $this->_product->getMetaDescription();
            } else {
                $descriptionTemplate = Mage::getStoreConfig('mageworx_seo/seosuite/product_meta_description_template');
                if ($descriptionTemplate) {
                    $template = Mage::getModel('seosuite/catalog_product_template_title');
                    $template->setTemplate($descriptionTemplate)
                            ->setProduct($this->_product);
                    $this->_data['description'] = $template->process();
                }
                if (empty($this->_data['description'])) {
                    $shortDescription = $this->getProductDescription();
                    if (Mage::getStoreConfigFlag('mageworx_seo/seosuite/product_meta_description') && !empty($shortDescription)) {
                        $this->_data['description'] = $shortDescription;
                    }
                }
            }
        }
        
        $this->_convertLayerMeta();
        
        if (empty($this->_data['description'])) {
            if ($this->_category = Mage::registry('current_category')) {
                $this->_data['description'] = $this->_category->getMetaDescription() ? $this->_category->getMetaDescription() : $oldDescription;
            } else {
                $this->_data['description'] = $oldDescription;
            }
        }

        $stripTags = new Zend_Filter_StripTags();

        return htmlspecialchars(html_entity_decode(preg_replace(array('/\r?\n/', '/[ ]{2,}/'), array(' ', ' '), $stripTags->filter($this->_data['description'])), ENT_QUOTES, 'UTF-8'));
    }

    private function _convertLayerMeta() {
        // if not product page
        if (Mage::registry('current_category')==null || Mage::registry('current_product')!=null) return false;
        
        $helper = Mage::helper('seosuite');
        $request = Mage::app()->getRequest();

        $hideAttributes = Mage::getStoreConfigFlag('mageworx_seo/seosuite/layered_hide_attributes');
        $layeredFriendlyUrls = Mage::getStoreConfigFlag('mageworx_seo/seosuite/layered_friendly_urls');
        
        
        $params = Mage::app()->getRequest()->getParams();
        
        if (Mage::registry('current_category') != null) {            
                
                // get meta title
                $metaTitle = Mage::registry('current_category')->getMetaTitle();
                if (!$metaTitle) $metaTitle = Mage::registry('current_category')->getName();                
                if (!Mage::getStoreConfigFlag('mageworx_seo/seosuite/enable_dynamic_meta_title')) {
                    $metaTitle = $this->__compile($metaTitle);
                }    
                $this->_data['title'] = $metaTitle;
                
                // get meta description
                $metaDescription = Mage::registry('current_category')->getMetaDescription();
                if (!$metaDescription) $metaDescription = Mage::registry('current_category')->getDescription();
                if (!Mage::getStoreConfigFlag('mageworx_seo/seosuite/enable_dynamic_meta_desc')) {
                    $metaDescription = $this->__compile($metaDescription);
                }
                $this->_data['description'] = $metaDescription;
        }

        if ($layeredFriendlyUrls) {
            $suffix = Mage::getStoreConfig('catalog/seo/category_url_suffix');
            $identifier = trim(str_replace($suffix, '', $request->getOriginalPathInfo()), '/');
            $urlSplit = explode('/l/', $identifier, 2);
            if (!isset($urlSplit[1])) {
                return false;
            }
            Varien_Autoload::registerScope('catalog');
            $productUrl = Mage::getModel('catalog/product_url');
            list($cat, $params) = $urlSplit;
            $layerParams = explode('/', $params);
            $_params = array();

            $attr = array();
            $descParts = array();

            $attributes = Mage::getModel('catalog/layer')->getFilterableAttributes();
            foreach ($attributes as $attribute) {
                $attr[$attribute->getAttributeCode()]['type'] = $attribute->getBackendType();
                $options = $attribute->getSource()->getAllOptions();
                foreach ($options as $option) {
                    $attr[$attribute->getAttributeCode()]['options'][$helper->formatUrlKey($option['label'])] = $option['label'];
                    $attr[$attribute->getAttributeCode()]['frontend_label'] = $attribute->getFrontendLabel();
                }
            }

            $titleParts = array(trim($this->_data['title']));
            if (isset($this->_data['description'])) {
                $descParts = array(trim($this->_data['description']));
            }
            if (count($layerParams)) {
                foreach ($layerParams as $params) {
                    $param = explode('-', $params, 2);
                    if (count($param) == 1) {
                        $cat = Mage::getModel('seosuite/catalog_category')
                                ->setStoreId(Mage::app()->getStore()->getId())
                                ->loadByAttribute('url_key', $productUrl->formatUrlKey($param[0]));
                        if ($cat && $cat->getId()) {
                            $titleParts[0] .= ' - ' . $cat->getName();
                            continue;
                        }
                        foreach ($attr as $attribute) {
                            if (isset($attribute['options'][current($param)])) {
                                $titleParts[] = $descParts[] = $attribute['options'][current($param)];
                                break;
                            }
                        }
                    } else {
                        if (isset($attr[$param[0]])) {
                            if ($param[0] == 'price') {
                                $multipliers = explode(',', $param[1]);
                                $frontendLabel = $hideAttributes ? '' : $attr[$param[0]]['frontend_label'];
                                $titleParts[] = $descParts[] = $frontendLabel . ' ' . Mage::app()->getStore()->formatPrice($multipliers[0] * $multipliers[1] - $multipliers[1], false) . ' - ' .
                                Mage::app()->getStore()->formatPrice($multipliers[0] * $multipliers[1], false);
                                continue;
                            }
                            $titleParts[] = $descParts[] = $attr[$param[0]]['frontend_label'] . ' - ' . $attr[$param[0]]['options'][$param[1]];
                        }
                    }
                }
            }
            if (Mage::getStoreConfigFlag('mageworx_seo/seosuite/enable_dynamic_meta_title')) {
                $this->_data['title'] = implode(', ', $titleParts);
            }

            if (Mage::getStoreConfigFlag('mageworx_seo/seosuite/enable_dynamic_meta_desc')) {
                $this->_data['description'] = implode(', ', $descParts);
            }
        }
    }

    protected function __parse($template) {
        $vars = array();
        preg_match_all('~(\[(.*?)\])~', $template, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            preg_match('~^((?:(.*?)\{(.*?)\}(.*)|[^{}]*))$~', $match[2], $params);
            array_shift($params);

            if (count($params) == 1) {
                $vars[$match[1]]['prefix'] = $vars[$match[1]]['suffix'] = '';
                $vars[$match[1]]['attributes'] = explode('|', $params[0]);
            } else {
                $vars[$match[1]]['prefix'] = $params[1];
                $vars[$match[1]]['suffix'] = $params[3];
                $vars[$match[1]]['attributes'] = explode('|', $params[2]);
            }
        }
        return $vars;
    }

    protected function __compile($template) {
        $vars = $this->__parse($template);
        foreach ($vars as $key => $params) {
            foreach ($params['attributes'] as $n => $attribute) {
                $value = '';
                $requestParams = Mage::app()->getRequest()->getParams();
                if (isset($requestParams[$attribute])) {
                    $value = $requestParams[$attribute];
                }

                if ($value) {
                    $value = $params['prefix'] . $value . $params['suffix'];
                    break;
                }
            }
            $template = str_replace($key, $value, $template);
        }
        return $template;
    }

}