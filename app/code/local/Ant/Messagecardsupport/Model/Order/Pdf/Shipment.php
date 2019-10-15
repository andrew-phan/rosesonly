<?php

require_once(Mage::getBaseDir() . '/lib/tcpdf/config/lang/eng.php');
require_once(Mage::getBaseDir() . '/lib/tcpdf/tcpdf.php');

class MYPDF extends TCPDF {

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-20);
        // Set font
        $this->SetFont('helvetica', '', 8);
        $this->SetTextColor(128, 128, 128);
        // Page number
        $this->Cell(0, 0, 'Singapore  |  www.rosesonly.com.sg', 0, 0, 'C');
        $this->Ln(5);
        $this->Cell(0, 0, 'Tel: (65) 6256 1818 Fax: (65) 6256 1612 Email: info.sg@rosesonlyasia.com', 0, 0, 'C');
        $this->setFooterData(array(0, 64, 255), array(0, 64, 128));
    }

}

class Ant_Messagecardsupport_Model_Order_Pdf_Shipment extends Mage_Sales_Model_Order_Pdf_Shipment {

    // code from original class (I have removed it for readability purposes)
//add this method
    public function addGiftMsg($page, $giftMessageSender, $giftMessageNote) {
        if (empty($giftMessageNote)) {
            return;
        }
        $pipfmText = $giftMessageSender . "***BREAK***" . "  " . "***BREAK***" . wordwrap($giftMessageNote, 100, "***BREAK***", true);
        $pipfmTextLines = array();
        $pipfmTextLines = explode("***BREAK***", $pipfmText);
        $i = 0;
        $pipfmTextLineStartY = 300;
        foreach ($pipfmTextLines as $pipfmTextLine) {
            $i++;
            //Bold only the first line
            if ($i == 1) {
                $this->_setFontBold_Modified($page, 10);
            } else {
                $this->_setFontRegular($page, 10);
            }
            $page->drawText($pipfmTextLine, 60, $pipfmTextLineStartY, 'UTF-8');
            $pipfmTextLineStartY = $pipfmTextLineStartY - 10;
        }
    }

    public function getPdf($shipments = array()) {
        $this->_beforeGetPdf();
        $this->_initRenderer('shipment');

        $pdf = new Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new Zend_Pdf_Style();
        $this->_setFontBold($style, 10);
        foreach ($shipments as $shipment) {
            if ($shipment->getStoreId()) {
                Mage::app()->getLocale()->emulate($shipment->getStoreId());
                Mage::app()->setCurrentStore($shipment->getStoreId());
            }
            $page = $this->newPage();
            $order = $shipment->getOrder();
            /* Add image */
            $this->insertLogo($page, $shipment->getStore());
            /* Add address */
            $this->insertAddress($page, $shipment->getStore());
            /* Add head */
            $this->insertOrder(
                    $page, $shipment, Mage::getStoreConfigFlag(self::XML_PATH_SALES_PDF_SHIPMENT_PUT_ORDER_ID, $order->getStoreId())
            );
            /* Add document text and number */
            $this->insertDocumentNumber(
                    $page, Mage::helper('sales')->__('DO No: ') . $shipment->getIncrementId()
            );
            /* Add table */
            $this->_drawHeader($page);
            /* Add body */
            foreach ($shipment->getAllItems() as $item) {
                if ($item->getOrderItem()->getParentItem()) {
                    continue;
                }
                /* Draw item */
                $page->setLineColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
                $this->_drawItem($item, $page, $order);
                $page = end($pdf->pages);
            }
        }

        /*
          $message = Mage::getModel('giftmessage/message');
          $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
          $page->setLineColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
          $page->drawRectangle(25, $this->y - 5, 570, $this->y + 10);

          $gift_message_id = $shipment->getOrder()->getGiftMessageId();
          $this->_setFontRegular($page, 9);
          $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
          if (!is_null($gift_message_id)) {
          $message->load((int) $gift_message_id);
          $gift_sender = $message->getData('sender');
          $gift_recipient = $message->getData('recipient');
          $gift_message = $message->getData('message');
          $page->drawText(Mage::helper('sales')->__('Message from:'), 35, $this->y, 'UTF-8');
          $page->drawText(Mage::helper('sales')->__($gift_sender), 120, $this->y, 'UTF-8');
          //$this->y -=10;
          $page->drawText(Mage::helper('sales')->__('Message to:'), 240, $this->y, 'UTF-8');
          $page->drawText(Mage::helper('sales')->__($gift_recipient), 300, $this->y, 'UTF-8');
          $this->y -=20;
          $page->drawText(Mage::helper('sales')->__('Message:'), 35, $this->y, 'UTF-8');
          $page->drawText(Mage::helper('sales')->__($gift_message), 120, $this->y, 'UTF-8');
          //echo $gift_message;
          }
         */
        $this->y -=30;

        $this->_setFontBold($page, 12);
        $page->drawText(Mage::helper('sales')->__('Delivery remarks:'), 35, $this->y, 'UTF-8');
        $this->_setFontRegular($page, 10);
        /*
          foreach ($order->getAllStatusHistory() as $orderComment) {
          if (strlen($orderComment->getComment()) > 0)
          $page->drawText($orderComment->getComment(), 35, $this->y -= 15, 'UTF-8');
          }
         */
        
        $this->y -= 50;
        if ($this->y < 300) {
            $page = $this->newPage();

            /* Add image */
            $this->insertLogo($page, $shipment->getStore());

            /* Add address */
            $this->insertAddress($page, $shipment->getStore());
        }
        $this->_setFontRegular($page, 10);
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
        $page->drawRectangle(25, $this->y - 20, 570, $this->y - 250);
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->_setFontBold($page, 10);
        $page->drawText(Mage::helper('sales')->__('We hope you enjoy our roses!'), 35, $this->y-=40, 'UTF-8');
        $page->drawText(Mage::helper('sales')->__('Please certify that the gift is received in good order and condition.'), 35, $this->y-=15, 'UTF-8');

        $this->y -= 150;
        $page->setLineWidth(2);
        $page->drawLine(40, $this->y, 165, $this->y);
        $page->drawLine(220, $this->y, 345, $this->y);
        $page->drawLine(400, $this->y, 525, $this->y);
        $page->setLineWidth(0.5);
        $page->drawText('Date & Time', 50, $this->y-=15, 'UTF-8');
        $page->drawText('Recipient Name & Signature', 223, $this->y, 'UTF-8');
        $page->drawText('Company Stamp', 415, $this->y, 'UTF-8');
        $this->_afterGetPdf();
        if ($shipment->getStoreId()) {
            Mage::app()->getLocale()->revert();
        }
        return $pdf;
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
        //$page->drawRectangle(25, $top, 570, $top - 20);
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->setDocHeaderCoordinates(array(25, $top, 570, $top - 55));
        $this->_setFontBold($page, 10);

        if ($putOrderId) {
            $page->drawText(
                    Mage::helper('sales')->__('SO No: ') . $order->getRealOrderId(), 370, ($top -= 15), 'UTF-8'
            );
        }
        $this->_setFontRegular($page, 10);
        /*
          $page->drawText(
          Mage::helper('sales')->__('Order Date: ') . Mage::helper('core')->formatDate(
          $order->getCreatedAtStoreDate(), 'medium', false
          ), 35, ($top -= 15), 'UTF-8'
          );
         */
        $top -= 10;
        $page->setFillColor(new Zend_Pdf_Color_Rgb(0, 0, 0));
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

        /* Hoang add */
        //$_order = Mage::registry('current_order');
        $orderid = $order->getId();
        $m_orders = Mage::getModel('onestepcheckout/onestepcheckout')->getCollection()->addFieldToFilter('sales_order_id', $orderid);
        $o = '';

        foreach ($m_orders as $m_order) { //$orders chua nhieu doi tuong. va` chi lay dc doi tuong con qua foreach
            $o = $m_order;           
        }
        $this->_setFontBold($page, 10);
        $page->drawText('Del Date: ', 35, ($top - 15), 'UTF-8');
        $this->_setFontRegular($page, 10);
        $page->drawText(Mage::getModel('core/date')->date('d-m-Y', strtotime(
                                $o->getMwDeliverydateDate())), 100, ($top - 15), 'UTF-8');
        $this->_setFontBold($page, 10);
        $page->drawText('Del Time: ', 35, ($top - 30), 'UTF-8');
        $this->_setFontRegular($page, 10);
        $page->drawText($o->getMwDeliverydateTime(), 100, ($top - 30), 'UTF-8');
        $this->_setFontBold($page, 10);
        $page->drawText('Zone    : ', 35, ($top - 45), 'UTF-8');
        $this->_setFontBold($page, 10);
        $page->drawLine(25, ($top - 50), 570, ($top - 50));
        /* end */

        $s_address = $order->getShippingAddress();
        $b_address = $order->getBillingAddress();
        /* Shipping Address and Method */
        if (!$order->getIsVirtual()) {
            /* Shipping Address */
            $shippingAddress = $this->_formatAddress($order->getShippingAddress()->format('pdf'));
            $shippingMethod = $order->getShippingDescription();
        }

        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->_setFontBold($page, 12);
        //$page->drawText(Mage::helper('sales')->__('Sold to:'), 35, ($top - 15), 'UTF-8');

        if (!$order->getIsVirtual()) {
            $page->drawText(Mage::helper('sales')->__('Sent To:'), 35, ($top - 65), 'UTF-8');
        } else {
            $page->drawText(Mage::helper('sales')->__('Payment Method:'), 285, ($top - 65), 'UTF-8');
        }


        $addressesHeight = $this->_calcAddressHeight($billingAddress);
        if (isset($shippingAddress)) {
            $addressesHeight = max($addressesHeight, $this->_calcAddressHeight($shippingAddress));
        }

        $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
        //$page->drawRectangle(25, ($top - 25), 570, $top - 33 - $addressesHeight);
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->_setFontRegular($page, 10);
        $this->y = $top - 80;
        $addressesStartY = $this->y;

        $addressesEndY = $this->y;

        if (!$order->getIsVirtual()) {
            /*
              $this->y = $addressesStartY;
              foreach ($shippingAddress as $value) {
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

            //print shpping address
            //print contact Person
            $this->_setFontRegular($page, 10);
            $page->drawText($s_address->getData('firstname') . ' ' . $s_address->getData('middlename').' '.$s_address->getData('lastname'), 35, $this->y, 'UTF-8');

            // address
            $this->_setFontBold($page, 10);
            $page->drawText(Mage::helper('sales')->__('Addresss:'), 35, $this->y-=20, 'UTF-8');
            if (strlen($s_address->getData('company')) > 0) {
                $this->_setFontRegular($page, 10);
                $page->drawText($s_address->getData('company'), 35, $this->y-=15, 'UTF-8');
            }
            $this->_setFontRegular($page, 10);
            //$page->drawText($s_address->getData('street'), 35, $this->y-=15, 'UTF-8');
            $page->drawText(preg_replace("/[\n\r]/", ", ", $s_address->getData('street')), 35, $this->y-=15, 'UTF-8');

            $countryName = Mage::getModel('directory/country')->load($s_address->getCountry())->getName();
            $this->_setFontRegular($page, 10);
            $page->drawText($countryName . ' ' . $s_address->getData('postcode'), 35, $this->y-=15, 'UTF-8');

            //print telephone
            $this->_setFontBold($page, 10);
            $page->drawText(Mage::helper('sales')->__('Tel:'), 35, $this->y-=20, 'UTF-8');
            $this->_setFontRegular($page, 10);
            $page->drawText($s_address->getData('telephone'), 35, $this->y-=15, 'UTF-8');

            //$addressesEndY = min($addressesEndY, $this->y);
            //$this->y = $addressesEndY;

            $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
            $page->setLineWidth(0.5);



            $message = Mage::getModel('giftmessage/message');
            $gift_message_id = $shipment->getOrder()->getGiftMessageId();

            if (!is_null($gift_message_id)) {

                $this->_setFontBold($page, 12);
                $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
                $page->drawText(Mage::helper('sales')->__('From:'), 35, $this->y-=20, 'UTF-8');
                $this->_setFontRegular($page, 10);

                $message->load((int) $gift_message_id);
                $gift_sender = $message->getData('sender');
                $gift_recipient = $message->getData('recipient');
                $gift_message = $message->getData('message');

                $page->drawText($gift_sender, 35, $this->y-=15, 'UTF-8');
            }



            $page->drawLine(25, $this->y - 10, 570, $this->y - 10);


            $this->y -= 80;

            $this->_setFontRegular($page, 10);
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        } else {
            $yPayments = $addressesStartY;
            $paymentLeft = 285;
        }
        $addressesEndY = $this->y;
    }

    /**
     * Insert title and number for concrete document type
     *
     * @param  Zend_Pdf_Page $page
     * @param  string $text
     * @return void
     */
    public function insertDocumentNumber(Zend_Pdf_Page $page, $text) {
        //$page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
        $this->_setFontBold($page, 10);
        $docHeader = $this->getDocHeaderCoordinates();
        $page->drawText($text, 35, $docHeader[1] - 15, 'UTF-8');

        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0.7));
        $this->_setFontBold($page, 15);
        $page->drawText(
                Mage::helper('sales')->__('Delivery Order'), 35, $docHeader[1] + 15, 'UTF-8'
        );

        $this->_setFontRegular($page, 10);
    }

    /**
     * Draw table header for product items
     *
     * @param  Zend_Pdf_Page $page
     * @return void
     */
    protected function _drawHeader(Zend_Pdf_Page $page) {
        /* Add table head */
        $this->_setFontRegular($page, 10);
        $page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
        $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $this->y += 60;
        $page->drawRectangle(25, $this->y, 570, $this->y - 15);
        $this->y -= 10;
        $page->setFillColor(new Zend_Pdf_Color_RGB(0, 0, 0));

        //columns headers
        $lines[0][] = array(
            'text' => Mage::helper('sales')->__('Gift Description'),
            'feed' => 100,
        );

        $lines[0][] = array(
            'text' => Mage::helper('sales')->__('Quantity'),
            'feed' => 35
        );

        $lineBlock = array(
            'lines' => $lines,
            'height' => 10
        );

        $this->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->y -= 20;
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

    public function createPDF($orders = array()) {
        foreach ($orders as $obj) {
            if ($obj instanceof Mage_Sales_Model_Order) {
                $shipment = null;
                $order = $obj;
            } elseif ($obj instanceof Mage_Sales_Model_Order_Shipment) {
                $shipment = $obj;
                $order = $shipment->getOrder();
            }

            $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);

            // set document information
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('Roses Only Singapore.');
            $pdf->SetTitle('Sales Order');
            $pdf->SetSubject('Sales Order');
            $pdf->SetKeywords('RosesOnly');

            $pdf->setPrintHeader(false);

            $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

            // set default monospaced font
            $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

            //set margins
            $pdf->SetMargins(25, 15, 25);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

            //set auto page breaks
            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

            //set image scale factor
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

            //set some language-dependent strings
            $pdf->setLanguageArray($l);

            // ---------------------------------------------------------
            // set font
            //$pdf->SetFont('dejavusans', '', 10);
            $pdf->SetFont('helvetica', '', 10);

            $pdf->AddPage();

            /* Add image */
            $this->insertHtmlLogo($pdf, $order);

            /* Add head */
            $this->insertHtmlOrder($pdf, $order);

            $shipments = Mage::getResourceModel('sales/order_shipment_collection')
                    ->setOrderFilter($order)
                    ->load();

            $html .= '<div style="font-weight:bold;">DELIVERY REMARKS :</div>';
            if ($shipments && sizeof($shipments) > 0) {
                foreach ($shipments as $shipment) {
                    foreach ($shipment->getAllTracks() as $tracknum) {
                        $html .= $tracknum->getNumber().'<br/>';
                    }
                    
                    foreach ($shipment->getCommentsCollection() as $_comment){
                        $html .= $_comment->getComment().'<br/>';
                    }
                }
            }

            /*
            foreach ($order->getAllStatusHistory() as $orderComment) {
              if (strlen($orderComment->getComment()) > 0)
              $html .= $orderComment->getComment();
              } 
              */
            $html .= '<div style="border:solid 1px #333;padding:10px;">
                <div style="margin-left:10px;"><b>&nbsp;We hope you enjoy our roses!</b></div>
                <div style="margin-left:10px;"><b>&nbsp;Please certify that the gift is received in good order and condition.</b></div>
                <br/><br/><br/><br/><br/><br/><br/>
                <div>
                    <table>
                        <tr>
                            <td style="text-align:center;width:150px;border-top:solid 2px #333;padding-top:10px;"><br/><b>Date & Time</b></td>
                            <td style="width:20px;"></td>
                            <td style="text-align:center;width:200px;border-top:solid 2px #333;padding-top:10px;"><br/><b>Recipient Name & Signature</b></td>
                            <td style="width:20px;"></td>
                            <td style="text-align:center;width:150px;border-top:solid 2px #333;padding-top:10px;"><br/><b>Company Stamp</b></td>
                        </tr>
                    </table>
                </div></div>';

            $pdf->writeHTML($html, true, false, true, false, '');
            $pdf->lastPage();

            if ($shipments && sizeof($shipments) > 0) {
                //foreach ($shipments as $shipment) {
                //    $pdf->Output($shipment->getIncrementId() . '.pdf', 'I');
                //}
                $pdf->Output($order->getIncrementId() . '.pdf', 'I');                
            } else {
                echo 'No DO for ' . $order->getRealOrderId() . ' available now !';
            }
            exit();
        }
    }

    private function insertHtmlLogo(&$pdf, $order) {
        $image_file = K_PATH_IMAGES . 'smallheader.png';
        //$pdf->Image($image_file, 20, 10, 70, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        /* Hoang add */
        $_order = Mage::registry('current_order');
        $orderid = $_order->getId();
        $orders = Mage::getModel('onestepcheckout/onestepcheckout')->getCollection()->addFieldToFilter('sales_order_id', $orderid);
        $o = '';

        foreach ($orders as $m_order) { //$orders chua nhieu doi tuong. va` chi lay dc doi tuong con qua foreach
            $o = $m_order;
        }
        /* end */
        $shipments = Mage::getResourceModel('sales/order_shipment_collection')
                ->setOrderFilter($order)
                ->load();
        if ($shipments && sizeof($shipments) > 0) {
            $html .='<table>
                <tr>
                <td><img width="260px" src="' . $image_file . '"/></td>                
                <td style="text-align:right;">
                    <table>
                        <tr><td></td><td></td></tr>
                        <tr style="padding-top:10px;">
                            <td style="text-align:right;color:#ca9b9a;font-weight:bold;font-size:42px;">Sales Order No:</td>
                            <td style="text-align:left;color:#808080;font-weight:bold;font-size:40px;">' . $order->getRealOrderId() . '</td>
                        </tr>
                        <tr>
                            <td style="text-align:right;color:#ca9b9a;font-weight:bold;font-size:42px;">Delivery No:</td>
                            <td style="text-align:left;color:#808080;font-weight:bold;font-size:40px;">';
            foreach ($shipments as $shipment) {
                $html.= $shipment->getIncrementId() . '';
		break;
            }
            $html.= '</td>
                        </tr>
                        <tr style="display: none;">
                            <td style="text-align:right;color:#ca9b9a;font-weight:bold;font-size:42px;">Delivery Date:</td>
                            <td style="text-align:left;color:#808080;font-weight:bold;font-size:40px;">' . Mage::getModel('core/date')->date('d-m-Y', strtotime(
                                    $o->getMwDeliverydateDate())) . '</td>
                        </tr>
                        <tr style="display:none;">
                            <td style="text-align:right;color:#ca9b9a;font-weight:bold;font-size:42px;">Time Zone:</td>
                            <td style="text-align:left;color:#808080;font-weight:bold;font-size:40px;">' . $o->getMwDeliverydateTime() . '</td>
                        </tr>
                    </table>
                </td>                
                </tr></table>';
        } else {
            $html .= '<div style="text-align:center;color:red; font-weight:bold;">NO DELIVERY PDF FOR ' . $order->getRealOrderId() . ' AVAILABLE NOW</div>';
        }
        //$html .= '';
        $pdf->writeHTML($html, true, false, true, false, '');

        $_SESSION['delivery_date_time']=  Mage::getModel('core/date')->date('d-m-Y', strtotime(
            $o->getMwDeliverydateDate())) .'<br/>'. $o->getMwDeliverydateTime() ;
    }

    protected function insertHtmlOrder(&$pdf, $obj, $putOrderId = true) {
        if ($obj instanceof Mage_Sales_Model_Order) {
            $shipment = null;
            $order = $obj;
        } elseif ($obj instanceof Mage_Sales_Model_Order_Shipment) {
            $shipment = $obj;
            $order = $shipment->getOrder();
        }


        $b_address = $order->getBillingAddress();
        /* Payment */
        $p_info = Mage::helper('payment')->getInfoBlock($order->getPayment());
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

        /* Shipping Address and Method */
        if (!$order->getIsVirtual()) {
            /* Shipping Address */
            $shippingAddress = $this->_formatAddress($order->getShippingAddress()->format('pdf'));
            $s_address = $order->getShippingAddress();
            $shippingMethod = $order->getShippingDescription();
        }

        //print contact Person
        $order_by_address .= $b_address->getData('firstname') . ' ' .$b_address->getData('middlename').' '.$b_address->getData('lastname');

        //print address
        $order_by_address .= '<br/>' . preg_replace("/[\n\r]/", ", ", $b_address->getData('street'));

        //print company
        $order_by_address .= '<br/>' . $b_address->getCompany();
        $countryName = Mage::getModel('directory/country')->load($b_address->getCountry())->getName();
        $order_by_address .= '<br/>' . $countryName . ' ' . $b_address->getData('postcode');

        //print telephone
        $order_by_address .= '<br/>' . $b_address->getData('telephone');

        //print email
        $order_by_address .= '<br/>' . $b_address->getData('email');


        //print shipping address
        //print contact Person
        $sent_to_address .= $s_address->getData('firstname').' '.$s_address->getData('middlename').' '.$s_address->getData('lastname');

        // Hau Vo: fix special character
        $sent_to_address = $this->fixSpecialCharacter($sent_to_address);

        //print address
        //print company
        $sent_to_address .='<br/>' . $this->fixSpecialCharacter($s_address->getCompany());
        
        $sent_to_address .= '<br/>' . preg_replace("/[\n\r]/", ", ", $this->fixSpecialCharacter($s_address->getData('street')));

        $sent_countryName = Mage::getModel('directory/country')->load($s_address->getCountry())->getName();

        $sent_countryName = $this->fixSpecialCharacter($sent_countryName);

        $sent_to_address .= '<br/>' . $sent_countryName . ' ' . $s_address->getData('postcode');

        //print telephone
        $sent_to_address .= '<br/>' . $this->fixSpecialCharacter($s_address->getData('telephone'));

        $html .= '<table>
                    <tr style="font-weight:bold;">                        
                        <td>SENT TO:</td>
                    </tr>
                    <tr>                        
                        <td>' . $sent_to_address . '</td>
                    </tr>
                </table>';

        $html .= '<br/><div style="margin-top:10px;border-bottom:solid 1px #333;"><b>From :</b>';

        $message = Mage::getModel('giftmessage/message');
        $gift_message_id = $order->getGiftMessageId();

        if (!is_null($gift_message_id)) {
            $message->load((int) $gift_message_id);
            $gift_sender = $message->getData('sender');
            //$gift_recipient = $message->getData('recipient');
            //$gift_message = $message->getData('message');

            // Hau Vo: fix special character
            $gift_sender = $this->fixSpecialCharacter($gift_sender);

            $html .= '<br/>' . $gift_sender;
        }
        $html .= '</div><br/>';
        $html .= '<table style="border:solid 1px #666;">
                    <tr style="font-weight:bold;text-align:center;background-color:#999;">
                        <td style="border-right:solid 1px #666;width:30px;">No</td>
                        <td style="border-right:solid 1px #666;width:300px;">Gift Description</td>
                        <td style="border-right:solid 1px #666;width:50px;">Qty</td>
                        <td style="border-right:solid 1px #666; width:185px;">Delivery Date & Time</td>

                    </tr>';


        /* Add body */
        $num = 1;
        $collection = Mage::getModel('freegift/rule')->getCollection();
        
        foreach ($order->getAllItems() as $item) {
            $is_gift = false;
            foreach ($collection as $key => $rule) {
                # code...
                if (strpos($rule['gift_product_ids'],$item->getProductId()) !== false && $rule['is_active'] ==1)
                    $is_gift = true;                
            }
            $html .='';
            if ($item->getParentItem()) {
                $product = Mage::getModel('catalog/product')->load($item->getProductId());
                if ($product->getAdditional() != 1) {
                    $html .='<tr>
                            <td style="border-right:solid 1px #666;"></td>
                            <td style="border-right:solid 1px #666;color:red;"></td>
                            <td style="border-right:solid 1px #666;"></td>
                            <td></td>
                        </tr>';

                    continue;
                }
            } else if ($item->getProductType() != 'simple' || $is_gift == true ) {
                $html .='<tr><td style="border-right:solid 1px #666;text-align:center;">' . $num++ . '</td>';
                if($item->getProductType()=='simple'){
                    /*$html .='<td style="border-right:solid 1px #666;"><font color="red">' . $item->getName().'</font>';*/
                }else
                    $html .='<td style="border-right:solid 1px #666;">' . $item->getName();
                
                $options = $this->getItemOptions($item);
                if ($options) {
                    foreach ($options as $option) {
                        //for ($index = 0; $index < sizeof($option); $index++) {
                        // draw options label
                        $html .= '<br/>' . $option['label'];

                        if ($option['value']) {
                            if (isset($option['print_value'])) {
                                $_printValue = $option['print_value'];
                            } else {
                                $_printValue = strip_tags($option['value']);
                            }
                            $values = explode(', ', $_printValue);
                            foreach ($values as $value) {
                                $html .= ' ' . $value;
                            }
                        }
                        //$html .= '</div>';
                        //}
                    }
                }
                if($item->getCustom_attribute()!=""){
                $delivery_date_time = $item->getCustom_attribute();
                    $delivery_date_time = trim($delivery_date_time);
                }
                else {
                    $delivery_date_time = $_SESSION['delivery_date_time'];
                    $delivery_date_time = trim($delivery_date_time);

                }


                $html .= '</td>';
                $html .='<td style="border-right:solid 1px #666;text-align:center;">' . ($item->getQtyOrdered() * 1) . '</td>';
                $html .='<td style="border-right:solid 1px #666;text-align:center;">' .$delivery_date_time . "<br>". '</td>';
                $html .='</tr>';
            }
        }
        $html .= '</table>';
        //return $html;
        $pdf->writeHTML($html, true, false, true, false, '');
    }

    public function getItemOptions($item) {
        $result = array();
        if ($options = $item->getProductOptions()) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }
        }
        return $result;
    }

    private function fixSpecialCharacter($str){
        //Hau Vo: fix special character
        $special_characters = array(
            "<",
            ">"
        );

        $replace_characters = array(
            "&lt;",
            "&gt;"
        );

        return str_replace($special_characters, $replace_characters, $str);
    }

}