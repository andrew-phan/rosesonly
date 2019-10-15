<?php
class Ant_Latepayment_Block_Adminhtml_Latepayment_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("latepayment_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("latepayment")->__("Item Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("latepayment")->__("Order Information"),
				"title" => Mage::helper("latepayment")->__("Order Information"),
				"content" => $this->getLayout()->createBlock("latepayment/adminhtml_latepayment_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}
