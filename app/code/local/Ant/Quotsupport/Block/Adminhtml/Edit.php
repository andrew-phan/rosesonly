<?php

class Ant_Quotsupport_Block_Adminhtml_Edit extends MDN_Quotation_Block_Adminhtml_Edit {

    public function __construct() {
		parent::__construct();
		
		$this->_addButton(
                'to_order',
                array(
                    'label' => 'Quote to Order',
					'class' => 'save',
                    'onclick' => "window.location.href='" . $this->getUrl('quotsupport/adminhtml_quotsupportbackend/', array('quote_id' => $this->getRequest()->getParam('quote_id'))) . "'"
                )
        );
	}
}