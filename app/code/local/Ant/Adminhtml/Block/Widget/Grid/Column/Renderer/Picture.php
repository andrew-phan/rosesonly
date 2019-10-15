<?php

class Ant_Adminhtml_Block_Widget_Grid_Column_Renderer_Picture extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row)
    {
        return $this->_getValue($row);
    } 
    protected function _getValue(Varien_Object $row)
    {       
        $_product = Mage::getModel('catalog/product')->load($row->getEntityId());
        $val = str_replace("no_selection", "", $_product->getThumbnail());
        $url = Mage::getBaseUrl('media') . 'catalog/product' . $val; 
        if ($_product->getThumbnail())
            $out = "<img src=". $url ." width='70px'/>"; 
        return $out;
    }

}
