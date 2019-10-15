<?php

/**
 * @category     Inchoo
 * @package     Inchoo Featured Products
 * @author        Domagoj Potkoc, Inchoo Team <web@inchoo.net>
 * @modified    Mladen Lotar <mladen.lotar@surgeworks.com>, Vedran Subotic <vedran.subotic@surgeworks.com>
 */
class Inchoo_FeaturedProducts_Block_Listing extends Mage_Catalog_Block_Product_Abstract {
    /*
     * Check sort option and limits set in System->Configuration and apply them
     * Additionally, set template to block so call from CMS will look like {{block type="featuredproducts/listing"}}
     */

    public function __construct() {
        $this->setTemplate('inchoo/featuredproducts/block_featured_products.phtml');

        $this->setLimit((int) Mage::getStoreConfig("featuredproducts/general/number_of_items"));
        $sort_by = Mage::getStoreConfig("featuredproducts/general/product_sort_by");
        $this->setItemsPerRow((int) Mage::getStoreConfig("featuredproducts/general/number_of_items_per_row"));

        switch ($sort_by) {
            case 0:
                $this->setSortBy("rand()");
                break;
            case 1:
                $this->setSortBy("updated_at desc");
                break;
            default:
                $this->setSortBy("rand()");
        }
    }

    /*
     * Load featured products collection
     * */

    protected function _beforeToHtml() {
        $collection = Mage::getResourceModel('catalog/product_collection');

        $attributes = Mage::getSingleton('catalog/config')
                ->getProductAttributes();
        $vs_Now = Mage::getSingleton('core/date')->gmtDate();
        $collection->addAttributeToSelect($attributes)
                ->addMinimalPrice()
                ->addFinalPrice()
                ->addTaxPercents()
                ->addAttributeToSelect('colorroses')
                ->addAttributeToSelect('feature_second')
                ->addAttributeToFilter('inchoo_featured_product', 1, 'left')
                ->addStoreFilter()
                ->addFieldToFilter('start_date', array(array("lt" => $vs_Now),array('eq'=>$vs_Now), array("null" => true)))
                ->addFieldToFilter('end_date', array(array("gt" => $vs_Now),array('eq'=>$vs_Now), array("null" => true)))
                ->getSelect()->order($this->getSortBy());;
                //->limit($this->getLimit());

        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);

        $this->_productCollection = $collection;

        $this->setProductCollection($collection);
        return parent::_beforeToHtml();
    }

    /*
     * Return label for CMS block output
     * */

    protected function getBlockLabel() {
        return $this->helper('featuredproducts')->getCmsBlockLabel();
    }

    /**
     * Retrieve loaded category collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _getProductCollection() {
        if (is_null($this->_productCollection)) {
            $layer = $this->getLayer();
            /* @var $layer Mage_Catalog_Model_Layer */
            if ($this->getShowRootCategory()) {
                $this->setCategoryId(Mage::app()->getStore()->getRootCategoryId());
            }

            // if this is a product view page
            if (Mage::registry('product')) {
                // get collection of categories this product is associated with
                $categories = Mage::registry('product')->getCategoryCollection()
                        ->setPage(1, 1)
                        ->load();
                // if the product is associated with any category
                if ($categories->count()) {
                    // show products from this category
                    $this->setCategoryId(current($categories->getIterator()));
                }
            }

            $origCategory = null;
            if ($this->getCategoryId()) {
                $category = Mage::getModel('catalog/category')->load($this->getCategoryId());
                if ($category->getId()) {
                    $origCategory = $layer->getCurrentCategory();
                    $layer->setCurrentCategory($category);
                }
            }
            $vs_Now = Mage::getSingleton('core/date')->gmtDate();
            $this->_productCollection = $layer->getProductCollection()
                    ->addAttributeToSelect('colorroses')
                    ->addAttributeToSelect('feature_second')
                    ->addFieldToFilter('start_date', array(array("lt" => $vs_Now), array("null" => true)))
                    ->addFieldToFilter('end_date', array(array("gt" => $vs_Now), array("null" => true)))
            ;

            $this->prepareSortableFieldsByCategory($layer->getCurrentCategory());

            if ($origCategory) {
                $layer->setCurrentCategory($origCategory);
            }
        }

        return $this->_productCollection;
    }

}