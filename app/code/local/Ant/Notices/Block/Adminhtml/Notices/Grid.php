<?php

class Ant_Notices_Block_Adminhtml_Notices_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId("noticesGrid");
        $this->setDefaultSort("notice_id");
        $this->setDefaultDir("ASC");
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel("notices/notices")->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn("notice_id", array(
            "header" => Mage::helper("notices")->__("ID"),
            "align" => "right",
            "width" => "50px",
            "index" => "notice_id",
        ));

        $this->addColumn("title", array(
            "header" => Mage::helper("notices")->__("Title"),
            "align" => "left",
            "index" => "title",
        ));

        $this->addColumn("description", array(
            "header" => Mage::helper("notices")->__("Description"),
            "align" => "left",
            "index" => "description",
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row) {
        return $this->getUrl("*/*/edit", array("id" => $row->getId()));
    }

}