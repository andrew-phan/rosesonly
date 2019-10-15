<?php

class MDN_OrderPreparation_Block_Packing_Index extends Mage_Core_Block_Template {

    public function getOrderInformationUrl() {
        return Mage::helper('adminhtml')->getUrl('OrderPreparation/Packing/OrderInformation');
    }

    public function getCheckedImageUrl() {
        return $this->getSkinUrl('images/scanner/ok.png');
    }

    public function getCommitPackingUrl() {
        return $this->getUrl('OrderPreparation/Packing/Commit');
    }

    public function getTranslateJson() {
        $translations = array(
            'Scan order to Pack' => $this->__('Scan order to Pack'),
            'Please scan products' => $this->__('Please scan products'),
            'An error occured' => $this->__('An error occured'),
            'Unknown barcode ' => $this->__('Unknown barcode '),
            'Product quantity already scanned !' => $this->__('Product quantity already scanned !'),
            ' scanned' => $this->__(' scanned'),
            ' are missing !' => $this->__(' are missing !'),
            );
        return Mage::helper('core')->jsonEncode($translations);
    }

}
