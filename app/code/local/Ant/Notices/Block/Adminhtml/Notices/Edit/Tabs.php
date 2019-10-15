<?php
class Ant_Notices_Block_Adminhtml_Notices_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("notices_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("notices")->__("Item Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("notices")->__("Item Information"),
				"title" => Mage::helper("notices")->__("Item Information"),
				"content" => $this->getLayout()->createBlock("notices/adminhtml_notices_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}
