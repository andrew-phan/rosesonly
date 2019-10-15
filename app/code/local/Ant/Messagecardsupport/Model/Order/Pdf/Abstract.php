<?php
abstract class Ant_Messagecardsupport_Model_Order_Pdf_Abstract extends Mage_Sales_Model_Order_Pdf_Abstract
{
    protected function insertLogo(&$page, $store = null)
    {
        $this->y = $this->y ? $this->y : 815;
        $image = Mage::getStoreConfig('sales/identity/logo', $store);
        if ($image) {
//            $image = Mage::getBaseDir('media') . '/sales/store/logo/' . $image;
            $image = Mage::getBaseDir('media') . '/wysiwyg/' . 'smallheader.png';
            if (is_file($image)) {
                $image       = Zend_Pdf_Image::imageWithPath($image);
                $top         = 830; //top border of the page
                $widthLimit  = 270; //half of the page width
                $heightLimit = 270; //assuming the image is not a "skyscraper"
                $width       = $image->getPixelWidth();
                $height      = $image->getPixelHeight();

                //preserving aspect ratio (proportions)
                $ratio = $width / $height;
                if ($ratio > 1 && $width > $widthLimit) {
                    $width  = $widthLimit;
                    $height = $width / $ratio;
                } elseif ($ratio < 1 && $height > $heightLimit) {
                    $height = $heightLimit;
                    $width  = $height * $ratio;
                } elseif ($ratio == 1 && $height > $heightLimit) {
                    $height = $heightLimit;
                    $width  = $widthLimit;
                }

                $y1 = $top - $height;
                $y2 = $top;
                $x1 = 25 + 280;
                $x2 = $x1 + $width;

                //coordinates after transformation are rounded by Zend
                $page->drawImage($image, $x1, $y1, $x2, $y2);
                

                $this->y = $y1 - 10;
            }
        }
    }
    
    protected function insertAddress(&$page, $store = null)
    {
//        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
//        $font = $this->_setFontRegular($page, 10);
//        $page->setLineWidth(0);
//        $this->y = $this->y ? $this->y : 815;
//        $top = 815;
//        foreach (explode("\n", Mage::getStoreConfig('sales/identity/address', $store)) as $value){
//            if ($value !== '') {
//                $value = preg_replace('/<br[^>]*>/i', "\n", $value);
//                foreach (Mage::helper('core/string')->str_split($value, 45, true, true) as $_value) {
//                    $page->drawText(trim(strip_tags($_value)),
//                        $this->getAlignRight($_value, 130, 440, $font, 10),
//                        $top,
//                        'UTF-8');
//                    $top -= 10;
//                }
//            }
//        }
//        $this->y = ($this->y > $top) ? $top : $this->y;
        $font = $this->_setFontRegular($page, 10);
        $text = 'Singapore | rosesonly.com.sg';
        $page->drawText($text,
                        $this->getAlignCenter($text, 332, 500, $font, 10),
                        20,
                        'UTF-8');
    }
}