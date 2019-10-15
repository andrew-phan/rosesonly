<?php
class Ant_Deliverymanagement_Block_Adminhtml_Arrangedriver_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("deliverymanagement_form", array("legend"=>Mage::helper("deliverymanagement")->__("Item information")));

				$fieldset->addField("name", "text", array(
				"label" => Mage::helper("deliverymanagement")->__("Deliverymanagement Name"),
				"class" => "required-entry",
				"required" => true,
				"name" => "name",
				));




				if (Mage::getSingleton("adminhtml/session")->getDeliverymanagementData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getDeliverymanagementData());
					Mage::getSingleton("adminhtml/session")->setDeliverymanagementData(null);
				} 
				elseif(Mage::registry("deliverymanagement_data")) {
				    $form->setValues(Mage::registry("deliverymanagement_data")->getData());
				}
				return parent::_prepareForm();
		}
}
