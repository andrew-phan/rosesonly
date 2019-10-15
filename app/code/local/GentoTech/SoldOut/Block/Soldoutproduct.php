<?php

class GentoTech_SoldOut_Block_Soldoutproduct extends Mage_Core_Block_Template
{
    public function _prepareLayout(){
        return parent::_prepareLayout();
    }

    public function getLableImage($fileName){
        //$fileName = Mage::getStoreConfig('productlable/sample/newimage');
        $width = 55;
        $height = 55;

        $folderURL = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
        $imageURL = $folderURL . $fileName;

        $basePath = Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA).DS.'theme'.DS.$fileName;
        $newPath = Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA) . DS . "resized" . DS . $fileName;
        //if width empty then return original size image's URL
        if ($width != '') {
            //if image has already resized then just return URL
            if (file_exists($basePath) && is_file($basePath) && !file_exists($newPath)) {
                $imageObj = new Varien_Image($basePath);
                $imageObj->constrainOnly(TRUE);
                $imageObj->keepAspectRatio(FALSE);
                $imageObj->keepFrame(FALSE);
                $imageObj->keepTransparency(TRUE);
                $imageObj->resize($width, $height);
                $imageObj->save($newPath);
            }
            $resizedURL = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . "resized/".$fileName;
        } else {
            $resizedURL = $imageURL;
        }
        return $resizedURL;
    }

    /*public function getLableImageDetail($fileName){
        //$fileName = Mage::getStoreConfig('productlable/sample/newimage');
        $width = 70;
        $height = 70;

        $folderURL = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
        $imageURL = $folderURL . $fileName;

        $basePath = Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA).DS.'theme'.DS.$fileName;
        $newPath = Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA) . DS . "resized" . DS . $fileName;
        //if width empty then return original size image's URL
        if ($width != '') {
            //if image has already resized then just return URL
            if (file_exists($basePath) && is_file($basePath) && !file_exists($newPath)) {
                $imageObj = new Varien_Image($basePath);
                $imageObj->constrainOnly(TRUE);
                $imageObj->keepAspectRatio(FALSE);
                $imageObj->keepFrame(FALSE);
                $imageObj->keepTransparency(TRUE);
                $imageObj->resize($width, $height);
                $imageObj->save($newPath);
            }
            $resizedURL = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . "resized/".$fileName;
        } else {
            $resizedURL = $imageURL;
        }
        return $resizedURL;
    }
*/

    public function isProductOutOfStockEnable($_product){
        if((Mage::getStoreConfig('soldout/sample/enableoutofstock')) && (!$_product->isAvailable())) {
            return true;
        }
        else{
            return false;
        }
    }

    public function getSoldOutImage(){
        $fileName = Mage::getStoreConfig('soldout/sample/outofstockimage');
        $resizedURL = $this->getLableImage($fileName);
        return $resizedURL;
    }

   /* public function getSoldOutImageDetail(){
        $fileName = Mage::getStoreConfig('soldout/sample/outofstockimagedetail');
        $resizedURL = $this->getLableImageDetail($fileName);
        return $resizedURL;
    }*/
}