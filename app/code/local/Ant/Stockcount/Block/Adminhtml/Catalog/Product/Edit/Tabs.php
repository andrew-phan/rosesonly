<?php

class Ant_Stockcount_Block_Adminhtml_Catalog_Product_Edit_Tabs extends Mage_Adminhtml_Block_Template implements Mage_Adminhtml_Block_Widget_Tab_Interface {

    /**
     * Set the template for the block
     *
     */
    public function _construct() {
        parent::_construct();
        $this->setTemplate('stockcount/catalog/product/tab.phtml');
    }

    /**
     * Retrieve the label used for the tab relating to this block
     *
     * @return string
     */
    public function getTabLabel() {
        return $this->__('Stock count');
    }

    /**
     * Retrieve the title used by this tab
     *
     * @return string
     */
    public function getTabTitle() {
        return $this->__('Click here to view stock count');
    }

    /**
     * Determines whether to display the tab
     * Add logic here to decide whether you want the tab to display
     *
     * @return bool
     */
    public function canShowTab() {
        if (($this->getProductType() == "bundle") || ($this->getProductType() == "configurable") || ($this->getProductType() == "grouped"))
            return true;
        return false;
    }

    /**
     * Stops the tab being hidden
     *
     * @return bool
     */
    public function isHidden() {
        return false;
    }

    public function getProductId() {
        $id = $this->getRequest()->getParam("id");
        return $id;
    }

    public function getProductType() {
        $id = $this->getProductId();
        $_product = Mage::getModel("catalog/product")->load($id);
        return $_product->getTypeId();
    }

    public function getGird() {
        
    }

    /**
     * AJAX TAB's
     * If you want to use an AJAX tab, uncomment the following functions
     * Please note that you will need to setup a controller to recieve
     * the tab content request
     *
     */
    /**
     * Retrieve the class name of the tab
     * Return 'ajax' here if you want the tab to be loaded via Ajax
     *
     * return string
     */
#   public function getTabClass()
#   {
#       return 'my-custom-tab';
#   }

    /**
     * Determine whether to generate content on load or via AJAX
     * If true, the tab's content won't be loaded until the tab is clicked
     * You will need to setup a controller to handle the tab request
     *
     * @return bool
     */
#   public function getSkipGenerateContent()
#   {
#       return false;
#   }

    /**
     * Retrieve the URL used to load the tab content
     * Return the URL here used to load the content by Ajax 
     * see self::getSkipGenerateContent & self::getTabClass
     *
     * @return string
     */
#   public function getTabUrl()
#   {
#       return null;
#   }
}