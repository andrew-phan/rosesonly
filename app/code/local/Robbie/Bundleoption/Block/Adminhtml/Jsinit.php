<?php  
/**
* 
* Author : RkD aka ROBIN
* Email  : 
* 
*/
/**
* 
*/
class Robbie_Bundleoption_Block_Adminhtml_Jsinit extends Mage_Adminhtml_Block_Template
{
	/**
     * Include JS in head if section is moneybookers
     */
    protected function _prepareLayout()
    {
        $section = $this->getAction()->getRequest()->getParam('section', false);
        if ($section == 'bundleoption_section_one') {
            $this->getLayout()
                ->getBlock('head')
                ->addJs('mage/adminhtml/robbie.js')
                ->addJs('bundleoption/addthis_widget.js');
        }
        parent::_prepareLayout();
    }

    /**
     * Print init JS script into body
     * @return string
     */
    protected function _toHtml()
    {
        $section = $this->getAction()->getRequest()->getParam('section', false);
        if ($section == 'bundleoption_section_one') {
            return parent::_toHtml();
        } else {
            return '';
        }
    }	

}
?>