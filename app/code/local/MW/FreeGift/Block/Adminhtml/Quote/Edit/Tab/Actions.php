<?php
class MW_FreeGift_Block_Adminhtml_Quote_Edit_Tab_Actions
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Prepare content for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('salesrule')->__('Actions');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('salesrule')->__('Actions');
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }
    
	public function __construct()
    {
        parent::__construct();
        $this->setTemplate('mw_freegift/freegift.phtml');
    }

    protected function _prepareForm()
    {
        $model = Mage::registry('current_freegift_quote_rule');

        //$form = new Varien_Data_Form(array('id' => 'edit_form1', 'action' => $this->getData('action'), 'method' => 'post'));
        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset('action_fieldset', array('legend'=>Mage::helper('salesrule')->__('Update prices using the following information')));

        $fieldset->addField('stop_rules_processing', 'select', array(
            'label'     => Mage::helper('salesrule')->__('Stop Further Rules Processing'),
            'title'     => Mage::helper('salesrule')->__('Stop Further Rules Processing'),
            'name'      => 'stop_rules_processing',
            'options'    => array(
                '1' => Mage::helper('salesrule')->__('Yes'),
                '0' => Mage::helper('salesrule')->__('No'),
            ),
        ));
        
       $fieldset->addField('gift_product_ids', 'hidden', array(
            'name' 		=> 'gift_product_ids',
            'required' 	=> false,
            'label' 	=> Mage::helper('salesrule')->__('Gift items'),
        ));
/*		$fieldset->addField('product_ids_tmp', 'hidden', array(
            'name' 	=> 'product_ids_tmp',
			'class'	=> 'mw_required',
            'label' => Mage::helper('salesrule')->__('Gift items'),
        ));*/
        $form->setValues($model->getData());

        if ($model->isReadonly()) {
            foreach ($fieldset->getElements() as $element) {
                $element->setReadonly(true, true);
            }
        }
        //$form->setUseContainer(true);

        $this->setForm($form);

        return parent::_prepareForm();
    }
    
	protected function _prepareLayout()
    {
        $this->setChild('grid',
            $this->getLayout()->createBlock('freegift/adminhtml_quote_edit_tab_actions_freegift','freegift_product_grid')
        );
        return parent::_prepareLayout();
    }

}
