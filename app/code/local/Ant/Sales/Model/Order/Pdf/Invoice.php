<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sales Order Invoice PDF model
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Ant_Sales_Model_Order_Pdf_Invoice extends Mage_Sales_Model_Order_Pdf_Invoice {

    /**
     * Draw header for item table
     *
     * @param Zend_Pdf_Page $page
     * @return void
     */
    protected function _drawHeader(Zend_Pdf_Page $page) {
        /* Add table head */
        $this->_setFontRegular($page, 10);
        $page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
        $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $this->y, 90, $this->y - 15);
        $page->drawRectangle(90, $this->y, 500, $this->y - 15);
        $page->drawRectangle(500, $this->y, 570, $this->y - 15);
        $this->y -= 10;
        $page->setFillColor(new Zend_Pdf_Color_RGB(0, 0, 0));

        //columns headers
        $lines[0][] = array(
            'text' => Mage::helper('sales')->__('Products'),
            'feed' => 100
        );

        $lines[0][] = array(
            'text' => Mage::helper('sales')->__('Quantity'),
            'feed' => 70,
            'align' => 'right'
        );

        $lines[0][] = array(
            //'text'  => Mage::helper('sales')->__('Subtotal'),
            'text' => Mage::helper('sales')->__('Amount'),
            'feed' => 565,
            'align' => 'right'
        );

        $lineBlock = array(
            'lines' => $lines,
            'height' => 5
        );

        $this->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->y -= 20;
    }

    /**
     * Return PDF document
     *
     * @param  array $invoices
     * @return Zend_Pdf
     */
    public function getPdf($invoices = array()) {
        $this->_beforeGetPdf();
        $this->_initRenderer('invoice');

        $pdf = new Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new Zend_Pdf_Style();
        $this->_setFontBold($style, 10);

        foreach ($invoices as $invoice) {
            if ($invoice->getStoreId()) {
                Mage::app()->getLocale()->emulate($invoice->getStoreId());
                Mage::app()->setCurrentStore($invoice->getStoreId());
            }
            $page = $this->newPage();
            $order = $invoice->getOrder();
            /* Add image */
            $this->insertLogo($page, $invoice->getStore());
            /* Add address */
            $this->insertAddress($page, $invoice->getStore());
            /* Add head */
            $this->insertOrder(
                    $page, $order, Mage::getStoreConfigFlag(self::XML_PATH_SALES_PDF_INVOICE_PUT_ORDER_ID, $order->getStoreId())
            );
            /* Add document text and number */
            $this->insertDocumentNumber(
                    $page, $invoice->getIncrementId()
            );
            /* Add table */
            $this->_drawHeader($page);
            /* Add body */
            $this->y +=10;
            foreach ($invoice->getAllItems() as $item) {
                if ($item->getOrderItem()->getParentItem()) {
                    continue;
                }
                /* Draw item */
                $start = $this->y + 10;
                $this->_drawItem($item, $page, $order);
                $end = $this->y - 5;
                $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
                $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.45));

                $page->drawLine(25, $start, 25, $end); // left
                $page->drawLine(90, $start, 90, $end); // left
                $page->drawLine(90, $start, 90, $end); // left
                $page->drawLine(500, $start, 500, $end); // right
                $page->drawLine(570, $start, 570, $end); // right
                $page->drawLine(25, $start, 570, $start); // top

                $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
                $page = end($pdf->pages);
            }
            $page->drawLine(25, $this->y + 10, 570, $this->y + 10); // top
            $page->drawLine(25, $this->y + 10, 25, $this->y - 35); // left
            $page->drawLine(90, $this->y + 10, 90, $this->y - 35); // middle
            $page->drawLine(500, $this->y + 10, 500, $this->y - 50); // middle
            $page->drawLine(570, $this->y, 570, $this->y - 50); // right
            $page->drawLine(25, $this->y - 35, 570, $this->y - 35); // bottom            
            $page->drawLine(500, $this->y - 50, 570, $this->y - 50); // bottom

            /* Add totals */
            $this->insertTotals($page, $invoice);
            if ($invoice->getStoreId()) {
                Mage::app()->getLocale()->revert();
            }
        }
        $this->_afterGetPdf();
        return $pdf;
    }

    /**
     * Create new page and assign to PDF object
     *
     * @param  array $settings
     * @return Zend_Pdf_Page
     */
    public function newPage(array $settings = array()) {
        /* Add new table head */
        $page = $this->_getPdf()->newPage(Zend_Pdf_Page::SIZE_A4);
        $this->_getPdf()->pages[] = $page;

        $this->y = 800;
        $font = $this->_setFontBold($page, 20);
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0.8));
        $page->drawText("Sales Invoice", 40, $this->y -= 35, 'UTF-8');
        $font = $this->_setFontRegular($page, 10);
        if (!empty($settings['table_header'])) {
            $this->_drawHeader($page);
        }
        return $page;
    }

    /**
     * Insert order to pdf page
     *
     * @param Zend_Pdf_Page $page
     * @param Mage_Sales_Model_Order $obj
     * @param bool $putOrderId
     */
    protected function insertOrder(&$page, $obj, $putOrderId = true) {
        if ($obj instanceof Mage_Sales_Model_Order) {
            $shipment = null;
            $order = $obj;
        } elseif ($obj instanceof Mage_Sales_Model_Order_Shipment) {
            $shipment = $obj;
            $order = $shipment->getOrder();
        }

        $this->y = $this->y ? $this->y : 815;
        $top = $this->y;

        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0.45));
        $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.45));
        //$page->drawRectangle(25, $top, 570, $top - 55);
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->setDocHeaderCoordinates(array(25, $top, 570, $top - 55));
        $this->_setFontRegular($page, 10);

        if ($putOrderId) {
            $this->_setFontBold($page, 10);
            $page->drawText('SO No', 35, ($top -= 15), 'UTF-8');
            $page->drawText(':', 100, $top, 'UTF-8');
            $this->_setFontRegular($page, 10);
            $page->drawText($order->getRealOrderId(), 110, $top, 'UTF-8');
        }

        $this->_setFontBold($page, 10);
        $page->drawText('Order Date', 35, ($top -= 15), 'UTF-8');
        $page->drawText(':', 100, $top, 'UTF-8');
        $this->_setFontRegular($page, 10);
        $page->drawText(Mage::getModel('core/date')->date('d/m/Y', strtotime(
                                $order->getCreatedAtStoreDate())), 110, $top, 'UTF-8'
        );
        /* Hoang add */

        $orderid = $order->getId();
        $_orders = Mage::getModel('onestepcheckout/onestepcheckout')->getCollection()->addFieldToFilter('sales_order_id', $orderid);

        $o = '';
        foreach ($_orders as $_order) { //$orders chua nhieu doi tuong. va` chi lay dc doi tuong con qua foreach
            $o = $_order;
        }
        $_dates = date_create($o->getMwDeliverydateDate());              
        $this->_setFontBold($page, 10);
        $page->drawText('Del Date', 285, $top, 'UTF-8');
        $page->drawText(':', 330, $top, 'UTF-8');
        $this->_setFontRegular($page, 10);
        $page->drawText(date_format($_dates, "d/m/Y"), 340, $top, 'UTF-8');
        //$page->drawText('Delivery Time: ' . $o->getMwDeliverydateTime(), 400, $yShipments - $topMargin + 20, 'UTF-8');
        /* end */
        $this->_setFontBold($page, 10);
        $page->drawText('Payment term', 35, ($top -= 15), 'UTF-8');
        $page->drawText(':', 100, $top, 'UTF-8');
        
        $page->drawLine(25, ($top - 5), 570, ($top - 5));

        $top -= 10;
        $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
        $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        //$page->drawRectangle(25, $top, 275, ($top - 25));
        //$page->drawRectangle(275, $top, 570, ($top - 25));

        /* Calculate blocks info */

        /* Billing Address */
        $billingAddress = $this->_formatAddress($order->getBillingAddress()->format('pdf'));

        /* Payment */
        $paymentInfo = Mage::helper('payment')->getInfoBlock($order->getPayment())
                ->setIsSecureMode(true)
                ->toPdf();
        $paymentInfo = htmlspecialchars_decode($paymentInfo, ENT_QUOTES);
        $payment = explode('{{pdf_row_separator}}', $paymentInfo);
        foreach ($payment as $key => $value) {
            if (strip_tags(trim($value)) == '') {
                unset($payment[$key]);
            }
        }
        reset($payment);
        
        $s_address = $order->getShippingAddress();
        $b_address = $order->getbillingAddress();
        /* Shipping Address and Method */
        if (!$order->getIsVirtual()) {
            /* Shipping Address */
            $shippingAddress = $this->_formatAddress($order->getShippingAddress()->format('pdf'));
            $shippingMethod = $order->getShippingDescription();
        }

        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->_setFontBold($page, 12);
        $page->drawText(Mage::helper('sales')->__('Bill to:'), 35, ($top - 15), 'UTF-8');

        
        if (!$order->getIsVirtual()) {
            $page->drawText(Mage::helper('sales')->__('Sent to:'), 285, ($top - 15), 'UTF-8');
        } else {
            $page->drawText(Mage::helper('sales')->__('Payment Method:'), 285, ($top - 15), 'UTF-8');
        }

        $addressesHeight = $this->_calcAddressHeight($billingAddress);
        if (isset($shippingAddress)) {
            $addressesHeight = max($addressesHeight, $this->_calcAddressHeight($shippingAddress));
        }

        $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
        //$page->drawRectangle(25, ($top - 25), 570, $top - 33 - $addressesHeight);
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->_setFontRegular($page, 10);
        $this->y = $top - 30;
        
       
        $addressesStartY = $this->y;
         /*
        foreach ($billingAddress as $value) {
            if ($value !== '') {
                $text = array();
                foreach (Mage::helper('core/string')->str_split($value, 45, true, true) as $_value) {
                    $text[] = $_value;
                }
                foreach ($text as $part) {
                    $page->drawText(strip_tags(ltrim($part)), 35, $this->y, 'UTF-8');
                    $this->y -= 15;
                }
            }
        }
        */
         //print contact Person
        $this->_setFontRegular($page, 10);
        $page->drawText($b_address->getData('firstname').' '.$b_address->getData('lastname'), 35, $this->y, 'UTF-8');
        $page->drawText($b_address->getCompany(), 35, $this->y-=15, 'UTF-8');
        
        //print address
        $this->_setFontBold($page, 10);
        $page->drawText(Mage::helper('sales')->__('Address:'), 35, $this->y-=15, 'UTF-8');
        if(strlen($b_address->getData('company'))>0){
                $this->_setFontRegular($page, 10);
                $page->drawText($b_address->getData('company'), 35, $this->y-=15, 'UTF-8');                       
        }
        $this->_setFontRegular($page, 10);
        //$page->drawText($b_address->getData('street'), 35, $this->y-=15, 'UTF-8');
        $page->drawText(preg_replace("/[\n\r]/",", ",$b_address->getData('street')), 35, $this->y-=15, 'UTF-8');
        
        $countryName = Mage::getModel('directory/country')->load($b_address->getCountry())->getName();
        $this->_setFontRegular($page, 10);
        $page->drawText($countryName.' '.$b_address->getData('postcode'), 35, $this->y-=15, 'UTF-8');
         
        $addressesEndY = $this->y;
        
        if (!$order->getIsVirtual()) {
            $this->y = $addressesStartY;
            /*
            foreach ($shippingAddress as $value) {
                if ($value !== '') {
                    $text = array();
                    foreach (Mage::helper('core/string')->str_split($value, 45, true, true) as $_value) {
                        $text[] = $_value;
                    }
                    foreach ($text as $part) {
                        $page->drawText(strip_tags(ltrim($part)), 285, $this->y, 'UTF-8');
                        $this->y -= 15;
                    }
                }
            }
            
            $addressesEndY = min($addressesEndY, $this->y);
            $this->y = $addressesEndY;
            */
            
             //print contact Person
            $this->_setFontRegular($page, 10);
            $page->drawText($s_address->getData('firstname').' '.$s_address->getData('lastname'), 285, $this->y, 'UTF-8');
            $page->drawText($s_address->getCompany(), 285, $this->y-=15, 'UTF-8');
            //$page->drawTextBlock($s_address->getCompany(), 285, $this->y-=15, 40, 250, Zend_Pdf_Page::ALIGN_LEFT);  

            //print address
            $this->_setFontBold($page, 10);
            $page->drawText(Mage::helper('sales')->__('Address:'), 285, $this->y-=15, 'UTF-8');
            if(strlen($b_address->getData('company'))>0){
                $this->_setFontRegular($page, 10);
                $page->drawText($s_address->getData('company'), 35, $this->y-=15, 'UTF-8');                       
            }
            $this->_setFontRegular($page, 10);
            //$page->drawText($s_address->getData('street'), 285, $this->y-=15, 'UTF-8');
            $page->drawText(preg_replace("/[\n\r]/",", ",$s_address->getData('street')), 285, $this->y-=15, 'UTF-8');
            
            $countryName = Mage::getModel('directory/country')->load($s_address->getCountry())->getName();
            $this->_setFontRegular($page, 10);
            $page->drawText($countryName.' '.$s_address->getData('postcode'), 285, $this->y-=15, 'UTF-8');
      
            $this->y -= 15;
            $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
            $page->setLineWidth(0.5);
            $page->drawLine(25, $this->y, 570, $this->y);

            $this->y -= 15;
            $this->_setFontBold($page, 12);
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
             
            $message = Mage::getModel('giftmessage/message');
           
            $gift_message_id = $order->getGiftMessageId();
            $this->_setFontRegular($page, 9);
          
            if (!is_null($gift_message_id)) {
                $message->load((int) $gift_message_id);
                $gift_sender = $message->getData('sender');
                $gift_recipient = $message->getData('recipient');
                $gift_message = $message->getData('message');
             
                $this->_setFontBold($page, 10);
                $page->drawText(Mage::helper('sales')->__('From: '), 35, $this->y, 'UTF-8');
                $this->_setFontRegular($page, 10);
                $rows = (int)(strlen ($gift_message)/135) +1;
                
                //$page->drawMultilineText(array($gift_message), 35, $this->y-=15, 'UTF-8');
                $this->y = $this->drawTextArea($page,$gift_message, 35, $this->y-=15, 15, 135 );
                $page->drawText($gift_sender, 35, $this->y, 'UTF-8');
            }
         
           
            $this->y -=10;
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));

            $this->_setFontRegular($page, 10);
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));

            $paymentLeft = 35;
            $yPayments = $this->y - 15;
        } else {
            $yPayments = $addressesStartY;
            $paymentLeft = 285;
        }
    }

    /**
     * Insert title and number for concrete document type
     *
     * @param  Zend_Pdf_Page $page
     * @param  string $text
     * @return void
     */
    public function insertDocumentNumber(Zend_Pdf_Page $page, $text) {
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->_setFontRegular($page, 10);
        $docHeader = $this->getDocHeaderCoordinates();
        $this->_setFontBold($page, 10);
        $page->drawText('Invoice No', 285, $docHeader[1] - 15, 'UTF-8');
        $page->drawText(':', 330, $docHeader[1] - 15, 'UTF-8');
        $this->_setFontRegular($page, 10);
        $page->drawText($text, 340, $docHeader[1] - 15, 'UTF-8');
    }

    /**
     * Insert address to pdf page
     *
     * @param Zend_Pdf_Page $page
     * @param null $store
     */
    protected function insertAddress(&$page, $store = null) {

        $font = $this->_setFontRegular($page, 10);
        $text = 'Singapore | rosesonly.com.sg';
        $page->drawText($text, $this->getAlignCenter($text, 0, 595, $font, 10), 35, 'UTF-8');

        $text = "Tel: (65) 6256 1818 Fax: (65) 6256 1612 Email: info.sg@rosesonlyasia.com";
        $page->drawText($text, $this->getAlignCenter($text, 0, 595, $font, 10), 20, 'UTF-8');
    }

    /**
     * Insert totals to pdf page
     *
     * @param  Zend_Pdf_Page $page
     * @param  Mage_Sales_Model_Abstract $source
     * @return Zend_Pdf_Page
     */
    protected function insertTotals($page, $source) {
        $order = $source->getOrder();
        $totals = $this->_getTotalsList($source);
        $lineBlock = array(
            'lines' => array(),
            'height' => 15
        );
        foreach ($totals as $total) {
            $total->setOrder($order)
                    ->setSource($source);

            if ($total->canDisplay()) {
                $total->setFontSize(10);
                foreach ($total->getTotalsForDisplay() as $totalData) {
                    $lineBlock['lines'][] = array(
                        array(
                            'text' => $totalData['label'],
                            'feed' => 475,
                            'align' => 'right',
                            'font_size' => $totalData['font_size'],
                            'font' => 'bold'
                        ),
                        array(
                            'text' => $totalData['amount'],
                            'feed' => 565,
                            'align' => 'right',
                            'font_size' => $totalData['font_size'],
                            'font' => 'bold'
                        ),
                    );
                }
            }
        }


        $page = $this->drawLineBlocks($page, array($lineBlock));


        $font = $this->_setFontRegular($page, 10);
        $page->drawText("Please Note: To ensure proper credit, kindly write your invoice number behind your crossed cheque made payable", 40, $this->y -= 20, 'UTF-8');
        $page->drawText("to: -", 40, $this->y -= 15, 'UTF-8');
        $font = $this->_setFontBold($page, 10);
        $page->drawText("Roses Only Asia Pte Ltd", 70, $this->y, 'UTF-8');


        $this->y -= 30;
        if ($this->y <200){
            $page = $this->newPage();
            $this->insertLogo($page, null);
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0.45));
            $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.45));
            //$page->drawRectangle(25, $top, 570, $top - 55);
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
            $this->setDocHeaderCoordinates(array(25, $top, 570, $top - 55));
        }
        $this->_setFontRegular($page, 10);


        
        $page->drawLine(350, $this->y, 570, $this->y);
        $text = "Served by RosesOnly.com.sg";
        $page->drawText($text, 400, $this->y -= 30, 'UTF-8');
        $text = "Thank You for sending a gift of the world’s finest roses!";
        $font = $this->_setFontBold($page, 12);
        $page->drawText($text, $this->getAlignCenter($text, 0, 595, $font, 10), $this->y -= 90, 'UTF-8');
        $text = "For the latest promotions, log on to www.RosesOnly.com.sg";
        $page->drawText($text, $this->getAlignCenter($text, 0, 595, $font, 10), $this->y -= 15, 'UTF-8');

        return $page;
    }

    public function drawTextArea($page, $text, $pos_x, $pos_y, $height, $length = 0, $offset_x = 0, $offset_y = 0)        {
        $x = $pos_x + $offset_x;
        $y = $pos_y + $offset_y;

        if ($length != 0) {
            $text = wordwrap($text, $length, "\n", false);
        }
        $token = strtok($text, "\n");

        while ($token != false) {
            $page->drawText($token, $x, $y);
            $token = strtok("\n");
            $y -= $height;
        }
        return $y;
    }
}
