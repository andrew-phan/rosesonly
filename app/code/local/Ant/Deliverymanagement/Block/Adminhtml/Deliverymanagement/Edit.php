<?php
	
class Ant_Deliverymanagement_Block_Adminhtml_Deliverymanagement_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "assign_id";
				$this->_blockGroup = "deliverymanagement";
				$this->_controller = "adminhtml_deliverymanagement";
				$this->_updateButton("save", "label", Mage::helper("deliverymanagement")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("deliverymanagement")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("deliverymanagement")->__("Save And Continue Edit"),
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
				if( Mage::registry("deliverymanagement_data") && Mage::registry("deliverymanagement_data")->getId() ){

				    return Mage::helper("deliverymanagement")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("deliverymanagement_data")->getName()));

				} 
				else{

				     return Mage::helper("deliverymanagement")->__("Add Item");

				}
		}
}