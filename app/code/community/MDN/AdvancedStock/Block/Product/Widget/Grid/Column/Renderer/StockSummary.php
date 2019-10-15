<?php

class MDN_AdvancedStock_Block_Product_Widget_Grid_Column_Renderer_StockSummary extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $retour = '<div style="white-space: nowrap;">';

        $collection = mage::helper('AdvancedStock/Product_Base')->getStocks($row->getId());
        foreach ($collection as $item) {
            if ($item->ManageStock()) {
                $qty = ((int) $item->getqty());
                $available = ((int) $item->getAvailableQty());
                $color = ($available > 0 ? 'green' : 'red');
                $retour .= '<font color="'.$color.'">'.$item->getstock_name() . ' : ' . $available . ' / ' . $qty . '</font><br>';
            }
        }

        $retour .= '</div>';

        return $retour;
    }

}