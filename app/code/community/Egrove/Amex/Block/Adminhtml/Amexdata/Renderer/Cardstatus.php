<?php
class Egrove_Amex_Block_Adminhtml_Amexdata_Renderer_Cardstatus extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $code = $row->getData($this->getColumn()->getIndex());
        return ($code=='Y')?'Verfied':'Not verfied';
    }    
}