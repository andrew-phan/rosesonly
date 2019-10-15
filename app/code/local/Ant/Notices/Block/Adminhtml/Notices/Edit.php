<?php
	
class Ant_Notices_Block_Adminhtml_Notices_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "notice_id";
				$this->_blockGroup = "notices";
				$this->_controller = "adminhtml_notices";
				$this->_updateButton("save", "label", Mage::helper("notices")->__("Save Notice"));
				$this->_updateButton("delete", "label", Mage::helper("notices")->__("Delete Notice"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("notices")->__("Save And Continue Edit"),
					"onclick"   => "saveAndContinueEdit()",
					"class"     => "save",
				), -100);



				$this->_formScripts[] = "

							function saveAndContinueEdit(){
								editForm.submit($('edit_form').action+'back/edit/');
							}
						";
		}

		public function getHeaderText()
		{
				if( Mage::registry("notices_data") && Mage::registry("notices_data")->getId() ){

				    return Mage::helper("notices")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("notices_data")->getName()));

				} 
				else{

				     return Mage::helper("notices")->__("Add Item");

				}
		}
}