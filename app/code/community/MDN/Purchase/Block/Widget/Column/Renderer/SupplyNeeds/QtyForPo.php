<?php

class MDN_Purchase_Block_Widget_Column_Renderer_SupplyNeeds_QtyForPo extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $value = '';
        $onChange = ' onchange="persistantGrid.logChange(this.name, \'' . $value . '\')"';
        $name = 'qty_' . $row->getId();
        $retour = '<input type="text" size="4" name="' . $name . '" id="' . $name . '" ' . $onChange . '>';

        $retour .= '<table cellpadding="0" cellspacing="0" border="0"><tr><td>';
        
        $onclick = "document.getElementById('" . $name . "').value = " . $row->getqty_min();
        $onclick .= ";persistantGrid.logChange('".$name."', '')";
        $retour .= '<input type="button" value="min" onclick="' . $onclick . '"> &nbsp;';

        $retour .= '</td><td>';
        
        $onclick = "document.getElementById('" . $name . "').value = " . $row->getqty_max();
        $onclick .= ";persistantGrid.logChange('".$name."', '')";
        $retour .= '<input type="button" value="max" onclick="' . $onclick . '">';

        $retour .= '</td></tr></table>';

        return $retour;
    }

}