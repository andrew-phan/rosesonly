<?php
class Ant_Deliverymanagement_Block_Adminhtml_Deliverymanagement_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("deliverymanagement_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("deliverymanagement")->__("Item Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("deliverymanagement")->__("Item Information"),
				"title" => Mage::helper("deliverymanagement")->__("Item Information"),
				"content" => $this->getLayout()->createBlock("deliverymanagement/adminhtml_deliverymanagement_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}
