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
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/**
 * Sales Order Invoice PDF model
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
require_once(Mage::getBaseDir() . '/lib/tcpdf/config/lang/eng.php');
require_once(Mage::getBaseDir() . '/lib/tcpdf/tcpdf.php');

class MYPDF extends TCPDF
{

    // Page footer
    public function Footer()
    {
        // Position at 15 mm from bottom
        $this->SetY(-70);
        // Set font
        $this->SetFont('helvetica', '', 8);
        $this->SetTextColor(128, 128, 128);
        // Page number
        $footer = "<b><u>ATM or Internet Fund Transfer</b></u><br/>
Transfer amount into our OCBC Current Account: <b>Roses Only Asia Pte Ltd – a/c 647069897001</b><br/>
Fax the deposit slip to us at (65) 6256 1612. Orders will only be processed after receiving the receipt.
Alternatively, you may also do an online transfer to our bank account if you’re using Internet Banking.<br/>
<b><u>Paying with Cheques</b></u><br/>
For Corporate orders, To ensure proper credit, kindly write your invoice number behind your crossed cheque made payable to: - <b>Roses Only Asia Pte Ltd.</b> Mail to us at the address above.<br/>
<b><u>Paying with Cash</b></u><br/>
For personal orders, we accept payment with Credit Card / Cash. Cash payment needs to be made in person at our office before delivery. <br/>
<b><u>Terms & Conditions</u></b><br/>
<b>1.	</b>If we are unable to deliver to a correct address or no one is around to receive your gift and the order is returned to our delivery officers, we can redeliver at a charge of 50% of the original order cost plus the additional delivery charge of $10.00 (w/gst $10.70).<br/>
<b>2.	</b>Should you need to cancel your confirmed order, if the order has already been prepared, we will need to charge 50% of the selling price.";

        $this->writeHTML($footer, true, false, true, false, '');
        $this->Cell(0, 0, 'Singapore  |  www.rosesonly.com.sg', 0, 0, 'C');
        $this->Ln(5);
        $this->Cell(0, 0, 'Tel: (65) 6256 1818 Fax: (65) 6256 1612 Email: info.sg@rosesonlyasia.com', 0, 0, 'C');
        $this->setFooterData(array(0, 64, 255), array(0, 64, 128));
    }

}

class Nastnet_OrderPrint_Model_Order_Pdf_Order extends Mage_Sales_Model_Order_Pdf_Abstract
{

    public function getPdf($orders = array())
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('order');

        $pdf = new Zend_Pdf();
        $style = new Zend_Pdf_Style();
        $this->_setFontBold($style, 10);

        foreach ($orders as $order) {
            $page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
            $pdf->pages[] = $page;

            //$order = $invoice->getOrder();

            /* Add image */
            $this->insertLogo($page, $order->getStore());

            /* Add address */
            $this->insertAddress($page, $order->getStore());

            /* Add head */
            $this->insertOrder($page, $order, Mage::getStoreConfigFlag(self::XML_PATH_SALES_PDF_INVOICE_PUT_ORDER_ID, $order->getStoreId()));

            $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
            $this->_setFontRegular($page, 10);

            /* Add table */
            $page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
            $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
            $page->setLineWidth(0.5);

            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));

            /* Add body */
            foreach ($order->getAllItems() as $item) {
                if ($item->getParentItem()) {
                    continue;
                }

                $shift = array();
                if ($this->y < 15) {
                    /* Add new table head */
                    $page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
                    $pdf->pages[] = $page;
                    $this->y = 800;
                }

                /* Draw item */
                $this->_setFontRegular($page, 10);
                $this->_drawItem($item, $page, $order);
            }

            /* Add totals */
            $this->insertTotals($page, $order);

            $this->_setFontBold($page, 12);
            $page->drawText(Mage::helper('sales')->__('Order/Delivery Remarks:'), 35, $this->y -= 40, 'UTF-8');
            $this->_setFontRegular($page, 10);
            foreach ($order->getAllStatusHistory() as $orderComment) {
                if (strlen($orderComment->getComment()) > 0)
                    $page->drawText($orderComment->getComment(), 35, $this->y -= 15, 'UTF-8');
            }
        }

        $this->_afterGetPdf();

        return $pdf;
    }

    protected function _drawItem(Varien_Object $item, Zend_Pdf_Page $page, Mage_Sales_Model_Order $order)
    {
        $type = $item->getProductType();
        $renderer = $this->_getRenderer($type);
        $renderer->setOrder($order);
        $renderer->setItem($item);
        $renderer->setPdf($this);
        $renderer->setPage($page);
        $renderer->setRenderedModel($this);

        $renderer->draw();
    }

    /**
     * Insert order to pdf page
     *
     * @param Zend_Pdf_Page $page
     * @param Mage_Sales_Model_Order $obj
     * @param bool $putOrderId
     */
    protected function insertOrder(&$page, $obj, $putOrderId = true)
    {
        if ($obj instanceof Mage_Sales_Model_Order) {
            $shipment = null;
            $order = $obj;
        } elseif ($obj instanceof Mage_Sales_Model_Order_Shipment) {
            $shipment = $obj;
            $order = $shipment->getOrder();
        }

        $this->y = $this->y ? $this->y : 815;
        $top = $this->y;

        $page->setFillColor(new Zend_Pdf_Color_Rgb(0, 0, 0));
        $this->setDocHeaderCoordinates(array(25, $top, 570, $top - 55));

        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0.7));
        $this->_setFontBold($page, 15);
        $page->drawText(
            Mage::helper('sales')->__('Sales Order Form'), 35, $top + 15, 'UTF-8'
        );

        $page->setFillColor(new Zend_Pdf_Color_Rgb(0, 0, 0));
        $this->_setFontBold($page, 10);
        $page->drawText(
            Mage::helper('sales')->__('Order Date: '), 35, ($top -= 15), 'UTF-8'
        );

        $this->_setFontRegular($page, 10);
        $page->drawText(
            Mage::getModel('core/date')->date('d-m-Y', strtotime(
                $order->getCreatedAtStoreDate())), 105, $top, 'UTF-8'
        );

        $this->_setFontBold($page, 10);
        $page->drawText(
            Mage::helper('sales')->__('Sale person: '), 220, $top, 'UTF-8'
        );
        $this->_setFontRegular($page, 10);
        $this->_setFontBold($page, 10);
        if ($putOrderId) {
            $page->drawText(
                Mage::helper('sales')->__('SO No: '), 400, $top, 'UTF-8'
            );
        }

        $this->_setFontRegular($page, 10);
        if ($putOrderId) {
            $page->drawText($order->getRealOrderId(), 450, $top, 'UTF-8'
            );
        }

        $top -= 10;
        $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
        $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);


        /* Calculate blocks info */

        /* Billing Address */
        $billingAddress = $this->_formatAddress($order->getBillingAddress()->format('pdf'));
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

        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->_setFontBold($page, 12);
        $page->drawText(Mage::helper('sales')->__('Order by:'), 35, ($top - 15), 'UTF-8');
        $page->setLineWidth(1);
        $page->drawLine(25, ($top - 20), 570, ($top - 20));
        $page->setLineWidth(0.5);

        $addressesHeight = $this->_calcAddressHeight($billingAddress);
        if (isset($shippingAddress)) {
            $addressesHeight = max($addressesHeight, $this->_calcAddressHeight($shippingAddress));
        }

        $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->_setFontRegular($page, 10);
        $this->y = $top - 40;
        $addressesStartY = $this->y;

        //print contact Person
        $this->_setFontBold($page, 10);
        $page->drawText(Mage::helper('sales')->__('Contact Person'), 35, $this->y, 'UTF-8');
        $page->drawText(Mage::helper('sales')->__(':'), 105, $this->y, 'UTF-8');
        $this->_setFontRegular($page, 10);
        $page->drawText($b_address->getData('firstname') . ' ' . $b_address->getData('lastname'), 110, $this->y, 'UTF-8');

        //print address
        $this->_setFontBold($page, 10);
        $page->drawText(Mage::helper('sales')->__('Address'), 35, $this->y -= 15, 'UTF-8');
        $page->drawText(Mage::helper('sales')->__(':'), 105, $this->y, 'UTF-8');
        $this->_setFontRegular($page, 10);
        $page->drawText(preg_replace("/[\n\r]/", ", ", $b_address->getData('street')), 110, $this->y, 'UTF-8');

        //print company
        $this->_setFontBold($page, 10);
        $page->drawText(Mage::helper('sales')->__('Company'), 330, $this->y, 'UTF-8');
        $page->drawText(Mage::helper('sales')->__(':'), 385, $this->y, 'UTF-8');
        $this->_setFontRegular($page, 10);
        $page->drawText($b_address->getCompany(), 390, $this->y, 'UTF-8');
        $countryName = Mage::getModel('directory/country')->load($b_address->getCountry())->getName();
        $this->_setFontRegular($page, 10);
        $page->drawText($countryName . ' ' . $b_address->getData('postcode'), 110, $this->y -= 15, 'UTF-8');

        $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
        $page->drawRectangle(300, $this->y - 35, 480, $this->y + 10);
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->_setFontBold($page, 10);
        $page->drawText(Mage::helper('sales')->__('Company stamp'), 330, $this->y, 'UTF-8');

        //print email
        $this->_setFontBold($page, 10);
        $page->drawText(Mage::helper('sales')->__('Email'), 35, $this->y -= 15, 'UTF-8');
        $page->drawText(Mage::helper('sales')->__(':'), 105, $this->y, 'UTF-8');
        $this->_setFontRegular($page, 10);
        $page->drawText($b_address->getData('email'), 110, $this->y, 'UTF-8');

        //print telephone
        $this->_setFontBold($page, 10);
        $page->drawText(Mage::helper('sales')->__('Tel'), 35, $this->y -= 15, 'UTF-8');
        $page->drawText(Mage::helper('sales')->__(':'), 105, $this->y, 'UTF-8');
        $this->_setFontRegular($page, 10);
        $page->drawText($b_address->getData('telephone'), 110, $this->y, 'UTF-8');

        $this->y -= 30;
        $addressesStartY = $this->y;
        $this->_setFontBold($page, 12);
        if (!$order->getIsVirtual()) {
            $page->drawText(Mage::helper('sales')->__('Delivery Instructions:'), 35, $this->y, 'UTF-8');
        } else {
            $page->drawText(Mage::helper('sales')->__('Payment Method:'), 35, $this->y, 'UTF-8');
        }
        $page->setLineWidth(1);
        $page->drawLine(25, $this->y - 5, 570, $this->y - 5);
        $page->setLineWidth(0.5);
        $this->_setFontRegular($page, 10);
        $addressesEndY = $this->y;
        /* Hoang add */
        $_order = Mage::registry('current_order');
        $orderid = $_order->getId();
        $orders = Mage::getModel('onestepcheckout/onestepcheckout')->getCollection()->addFieldToFilter('sales_order_id', $orderid);
        $o = '';

        foreach ($orders as $m_order) { //$orders chua nhieu doi tuong. va` chi lay dc doi tuong con qua foreach
            $o = $m_order;
        }
        $this->_setFontBold($page, 10);
        $page->drawText('Del Date', 35, $this->y -= 15, 'UTF-8');
        $page->drawText(':', 105, $this->y, 'UTF-8');
        $this->_setFontRegular($page, 10);
        $page->drawText(Mage::getModel('core/date')->date('d-m-Y', strtotime(
            $o->getMwDeliverydateDate())), 110, $this->y, 'UTF-8');
        $this->_setFontBold($page, 10);
        $page->drawText('Del Remarks', 330, $this->y, 'UTF-8');
        $page->drawText(':', 385, $this->y, 'UTF-8');

        $page->drawText('Del Time', 35, $this->y -= 15, 'UTF-8');
        $page->drawText(':', 105, $this->y, 'UTF-8');
        $this->_setFontRegular($page, 10);
        $page->drawText($o->getMwDeliverydateTime(), 110, $this->y, 'UTF-8');
        /* end */

        if (!$order->getIsVirtual()) {
            //print shipping address
            //print contact Person
            $this->_setFontBold($page, 10);
            $page->drawText(Mage::helper('sales')->__('Recipient Name'), 35, $this->y -= 15, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__(':'), 105, $this->y, 'UTF-8');
            $this->_setFontRegular($page, 10);
            $page->drawText($s_address->getData('firstname') . ' ' . $s_address->getData('lastname'), 110, $this->y, 'UTF-8');

            //print telephone
            $this->_setFontBold($page, 10);
            $page->drawText(Mage::helper('sales')->__('Tel'), 330, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__(':'), 385, $this->y, 'UTF-8');
            $this->_setFontRegular($page, 10);
            $page->drawText($s_address->getData('telephone'), 390, $this->y, 'UTF-8');

            //print company
            $this->_setFontBold($page, 10);
            $page->drawText(Mage::helper('sales')->__('Company'), 35, $this->y -= 15, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__(':'), 105, $this->y, 'UTF-8');
            $this->_setFontRegular($page, 10);
            $page->drawText($s_address->getCompany(), 110, $this->y, 'UTF-8');

            //print address
            $this->_setFontBold($page, 10);
            $page->drawText(Mage::helper('sales')->__('Address'), 35, $this->y -= 15, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__(':'), 105, $this->y, 'UTF-8');
            $this->_setFontRegular($page, 10);
            //$page->drawText($s_address->getData('street'), 110, $this->y, 'UTF-8');
            $page->drawText(preg_replace("/[\n\r]/", ", ", $s_address->getData('street')), 110, $this->y, 'UTF-8');

            $countryName = Mage::getModel('directory/country')->load($s_address->getCountry())->getName();
            $this->_setFontRegular($page, 10);
            $page->drawText($countryName . ' ' . $s_address->getData('postcode'), 110, $this->y -= 15, 'UTF-8');

            $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));

            $this->y -= 30;
            $this->_setFontBold($page, 12);
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
            $page->drawText(Mage::helper('sales')->__('Method of Payment'), 35, $this->y, 'UTF-8');

            $page->setLineWidth(1);
            $page->drawLine(25, $this->y - 5, 570, $this->y - 5);
            $page->setLineWidth(0.5);

            $page->drawText(Mage::helper('sales')->__('Payment mode'), 35, $this->y -= 20, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Card No'), 150, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Expiry Date'), 270, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Card Holder Name'), 350, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Trx Ind'), 500, $this->y, 'UTF-8');

            $this->y -= 15;
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));

            $this->_setFontRegular($page, 10);
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));

            $paymentLeft = 35;
            $yPayments = $this->y;
        } else {
            $yPayments = $addressesStartY;
            $paymentLeft = 285;
        }

        if ($p_info->getCcTypeName()) {
            $page->drawText($p_info->getCcTypeName(), 35, $yPayments, 'UTF-8');

            if ($p_info->getInfo())
                $page->drawText(sprintf('xxxx-xxxx-xxxx-%s', $p_info->getInfo()->getCcLast4()), 150, $yPayments, 'UTF-8');

            if ($p_info->getCcExpDate())
                $page->drawText($p_info->getInfo()->getCcExpMonth() . '/' . $p_info->getInfo()->getCcExpYear(), 270, $yPayments, 'UTF-8');

            if ($p_info->getInfo())
                $page->drawText($p_info->getInfo()->getCcOwner(), 350, $yPayments, 'UTF-8');
        } else {
            foreach ($payment as $value) {
                if (trim($value) != '') {
                    //Printing "Payment Method" lines
                    $value = preg_replace('/<br[^>]*>/i', "\n", $value);
                    foreach (Mage::helper('core/string')->str_split($value, 45, true, true) as $_value) {
                        $page->drawText(strip_tags(trim($_value)), $paymentLeft, $yPayments, 'UTF-8');
                        $yPayments -= 15;
                    }
                }
            }
        }

        $this->y = $yPayments;
    }

    protected function insertAddress(&$page, $store = null)
    {
        $font = $this->_setFontRegular($page, 9);
        $page->setFillColor(new Zend_Pdf_Color_Rgb(0.5, 0.5, 0.5));
        $yAddress = 210;
        $line = 12;
        $this->_setFontBold($page, 9);
        $page->drawText("ATM or Internet Fund Transfer", 50, $yAddress -= $line, 'UTF-8');

        $page->setLineWidth(0.5);
        $page->drawLine(50, $yAddress - 2, 170, $yAddress - 2);

        $this->_setFontRegular($page, 9);
        $page->drawText("Transfer amount into our OCBC Current Account :", 50, $yAddress -= $line, 'UTF-8');

        $this->_setFontBold($page, 9);
        $page->drawText("Roses Only Asia Pte Ltd - a/c 647069897001", 235, $yAddress, 'UTF-8');

        $this->_setFontRegular($page, 9);
        $page->drawText("Fax the deposit slip to us at (65) 6256 1612. Orders will only be processed after receiving the receipt.", 50, $yAddress -= $line, 'UTF-8');
        $page->drawText("Alternatively, you may also do an online transfer to our bank account if you're using Internet Banking", 50, $yAddress -= $line, 'UTF-8');

        $this->_setFontBold($page, 9);
        $page->drawText("Paying with Cheques", 50, $yAddress -= $line, 'UTF-8');

        $page->setLineWidth(0.5);
        $page->drawLine(50, $yAddress - 2, 130, $yAddress - 2);

        $this->_setFontRegular($page, 9);
        $page->drawText("For Corporate orders, To ensure proper credit, kindly write your invoice number behind your crossed cheque made payable ", 50, $yAddress -= $line, 'UTF-8');
        $page->drawText('to: - ', 50, $yAddress -= $line, 'UTF-8');

        $this->_setFontBold($page, 9);
        $page->drawText('Roses Only Asia Pte Ltd.', 70, $yAddress, 'UTF-8');

        $this->_setFontRegular($page, 9);
        $page->drawText('Mail to us at the address above.', 170, $yAddress, 'UTF-8');

        $this->_setFontBold($page, 9);
        $page->drawText("Paying with Cash", 50, $yAddress -= $line, 'UTF-8');

        $page->setLineWidth(0.5);
        $page->drawLine(50, $yAddress - 2, 120, $yAddress - 2);

        $this->_setFontRegular($page, 9);
        $page->drawText("For personal orders, we accept payment with Credit Card / Cash. Cash payment needs to be made in person at our office before delivery.", 50, $yAddress -= $line, 'UTF-8');

        $this->_setFontBold($page, 9);
        $page->drawText("Terms & Conditions", 50, $yAddress -= $line, 'UTF-8');

        $this->_setFontBold($page, 10);
        $page->drawText("1.", 50, $yAddress -= $line, 'UTF-8');
        $this->_setFontRegular($page, 8);
        $page->drawText("If we are unable to deliver to a correct address or no one is around to receive your gift and the order is returned to our delivery officers,", 60, $yAddress, 'UTF-8');
        $page->drawText("we can redeliver at a charge of 50% of the original order cost plus the additional delivery charge of $10.00 (w/gst $10.70).", 60, $yAddress -= $line, 'UTF-8');

        $this->_setFontBold($page, 10);
        $page->drawText("2.", 50, $yAddress -= $line, 'UTF-8');
        $this->_setFontRegular($page, 8);
        $page->drawText("Should you need to cancel your confirmed order, if the order has already been prepared, we will need to charge 50% of the selling price.", 60, $yAddress, 'UTF-8');

        $this->_setFontBold($page, 9);
        $text = "Singapore  |  rosesonly.com.sg";
        $page->drawText($text, $this->getAlignCenter($text, 10, 600, $font, 9), 38, 'UTF-8');

        $this->_setFontRegular($page, 9);
        $text = "Tel: (65) 6256 1818 Fax: (65) 6256 1612 Email: info.sg@rosesonlyasia.com";
        $page->drawText($text, $this->getAlignCenter($text, 10, 600, $font, 9), 25, 'UTF-8');
    }

    /**
     * Insert totals to pdf page
     *
     * @param  Zend_Pdf_Page $page
     * @param  Mage_Sales_Model_Abstract $source
     * @return Zend_Pdf_Page
     */
    protected function insertTotals($page, $source)
    {
        $order = $source->getOrder();
        $totals = $this->_getTotalsList($source);
        $lineBlock = array(
            'lines' => array(),
            'height' => 15
        );
        foreach ($totals as $total) {
            $total->setOrder($order)
                ->setSource($source);

            // only allow Grand Total to be displayed
            if ($total->getSourceField() != 'grand_total') {
                continue;
            }
            if ($total->canDisplay()) {
                $total->setFontSize(10);
                foreach ($total->getTotalsForDisplay() as $totalData) {
                    $lineBlock['lines'][] = array(
                        array(
                            //'text' => $totalData['label'],
                            'text' => 'Total Amount paid:',
                            'feed' => 475,
                            'align' => 'right',
                            'font_size' => $totalData['font_size'],
                            'font' => 'bold'
                        ),
                        array(
                            'text' => $totalData['amount'],
                            'feed' => 555,
                            'align' => 'right',
                            'font_size' => $totalData['font_size'],
                            'font' => 'bold'
                        ),
                    );
                }
            }
        }

        $this->y -= 20;
        $page = $this->drawLineBlocks($page, array($lineBlock));
        return $page;
    }

    public function getPdfm($orders = array(), $type = '')
    {
        foreach ($orders as $order) {
            $advance = Mage::getStoreConfig('mageworx_sales/advancemessage_settings' . $type . '/enable');
            if ($advance == 1) {
                $orientation = Mage::getStoreConfig('mageworx_sales/advancemessage_settings' . $type . '/orientation');
                $size = Mage::getStoreConfig('mageworx_sales/advancemessage_settings' . $type . '/size');
                $font_size = Mage::getStoreConfig('mageworx_sales/advancemessage_settings' . $type . '/font_size');
                $top = Mage::getStoreConfig('mageworx_sales/advancemessage_settings' . $type . '/top');
                $left = Mage::getStoreConfig('mageworx_sales/advancemessage_settings' . $type . '/left');
                $right = Mage::getStoreConfig('mageworx_sales/advancemessage_settings' . $type . '/right');
                $custom_size = Mage::getStoreConfig('mageworx_sales/advancemessage_settings' . $type . '/custom_size');

                if ($custom_size == 1) {
                    $width = Mage::getStoreConfig('mageworx_sales/advancemessage_settings' . $type . '/width');
                    $height = Mage::getStoreConfig('mageworx_sales/advancemessage_settings' . $type . '/height');
                    $page_layout = array($width, $height);
                    $pdf = new TCPDF($orientation, PDF_UNIT, $page_layout, true, 'UTF-8', false);
                } else if ($size == 'Envelope'){
                    $width = 152.4;
                    $height = 88.9;
                    $page_layout = array($width, $height);
                    $pdf = new TCPDF($orientation, PDF_UNIT, $page_layout, true, 'UTF-8', false);
                }else
                    $pdf = new TCPDF($orientation, PDF_UNIT, $size, true, 'UTF-8', false);

                $pdf->SetFont('helvetica', '', $font_size);
                $pdf->SetMargins($left, $top, $right);
                $pdf->SetHeaderMargin(0);
                $pdf->SetFooterMargin(0);
            } else {
                $pdf = new TCPDF('L', PDF_UNIT, 'A5', true, 'UTF-8', false);
                $font_size  = 10;
                $pdf->SetFont('helvetica', '', $font_size);
                //set margins
                $pdf->SetMargins(155, 35, 0);
                $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
                $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
            }

            // set document information
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('Roses Only Singapore.');
            $pdf->SetTitle('Sales Order');
            $pdf->SetSubject('Sales Order');
            $pdf->SetKeywords('RosesOnly');

            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);

            $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

            // set default monospaced font
            $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

            //set auto page breaks
            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

            //set image scale factor
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

            $pdf->AddPage();

            $message = Mage::getModel('giftmessage/message');
            $gift_message_id = $order->getGiftMessageId();

            if (!is_null($gift_message_id)) {
                $message->load((int)$gift_message_id);
                $gift_sender = $message->getData('sender');
                $gift_recipient = $message->getData('recipient');
                $gift_message = str_replace('\n', ' ', $message->getData('message'));

                $html = '<div style="width:100%; height:100%; margin:0; padding:0;">';
                if (!strpos($gift_recipient, '[]')) {
                    $html .= '<p style="text-align:left;font-size:'.$font_size.'px;">' . $gift_recipient . '</p>';
                }

                $html .= '<p style="text-align:center;font-weight:bold;font-size:'.$font_size.'px;">' . ($gift_message) . "</p>";

                if (strlen(trim($gift_sender)) != 0 && !strpos($gift_sender, '[]')) {
                    $html .= '<br/><p style="text-align:right;font-size:'.$font_size.'px;">' . $gift_sender . '</p>';
                }

                $html .= '<p style="text-align:right;font-size:'.$font_size.'px;">S/O : ' . $order->getIncrementId() . '</p>';
                $html .= '</div>';

                $pdf->writeHTML($html, true, false, true, false, '');
                $pdf->lastPage();

                $pdf->Output($order->getIncrementId() . '.pdf', 'I');
                exit();
            } else {
                echo 'No message';
            }
        }
    }

    public function drawTextArea($page, $text, $pos_x, $pos_y, $height, $length = 0, $offset_x = 0, $offset_y = 0)
    {
        $x = $pos_x + $offset_x;
        $y = $pos_y + $offset_y;

        if ($length != 0) {
            $text = wordwrap($text, $length, "\n\r", false);
        }
        $token = strtok($text, "\n\r");

        while ($token != false) {
            $font = $this->_setFontBold($page, 12);
            $page->drawText($token, $this->getAlignCenter($token, 180, 270, $font, 12), $y, 'UTF-8');
            $token = strtok("\n\r");
            $y -= $height;
        }
        return $y;
    }

    public function createPDF($orders = array())
    {
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

        $pdf->SetFont('helvetica', '', 10);

        foreach ($orders as $order) {

            $pdf->AddPage();

            /* Add image */
            $this->insertHtmlLogo($pdf, $order);

            /* Add head */
            $this->insertHtmlOrder($pdf, $order);

            /* Add totals */
            $this->insertHtmlTotals($pdf, $order);


            $html = '<div style="font-weight:bold;">ORDER/DELIVERY REMARKS:</div>';
            foreach ($order->getAllStatusHistory() as $orderComment) {
                if (strlen($orderComment->getComment()) > 0)
                    $html .= $orderComment->getComment();
            }

            $pdf->writeHTML($html, true, false, true, false, '');

            $pdf->lastPage();
        }

        if (sizeof($orders) == 1)
            $pdf->Output($orders[0]->getIncrementId() . '.pdf', 'I');
        else
            $pdf->Output($orders[0]->getIncrementId() . '-' . $orders[sizeof($orders) - 1]->getIncrementId() . '.pdf', 'I');
        exit();
    }

    private function insertHtmlLogo(&$pdf, $order)
    {
        $image_file = K_PATH_IMAGES . 'smallheader.png';

        $html = '<table>
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
                            <td style="text-align:right;color:#ca9b9a;font-weight:bold;font-size:42px;">Invoice No:</td>
                            <td style="text-align:left;color:#808080;font-weight:bold;font-size:40px;">';
        if ($order->hasInvoices()) {
            // "$_eachInvoice" is each of the Invoice object of the order "$order"
            foreach ($order->getInvoiceCollection() as $_eachInvoice) {
                $html .= $_eachInvoice->getIncrementId() . ' ';
            }
        }
        $html .= '</td>
                        </tr>
                        <tr>
                            <td style="text-align:right;color:#ca9b9a;font-weight:bold;font-size:42px;">Order Date:</td>
                            <td style="text-align:left;color:#808080;font-weight:bold;font-size:40px;">' . Mage::getModel('core/date')->date('d-m-Y', strtotime(
                $order->getCreatedAt())) . '</td>
                        </tr>
                    </table>
                </td>                
                </tr></table>';
        $pdf->writeHTML($html, true, false, true, false, '');
    }

    protected function insertHtmlOrder(&$pdf, $obj)
    {
        $order = null;
        if ($obj instanceof Mage_Sales_Model_Order) {
            $shipment = null;
            $order = $obj;
        } elseif ($obj instanceof Mage_Sales_Model_Order_Shipment) {
            $shipment = $obj;
            $order = $shipment->getOrder();
        }
        /* Hoang add */

        $orderid = $order->getId();
        $orders = Mage::getModel('onestepcheckout/onestepcheckout')->getCollection()->addFieldToFilter('sales_order_id', $orderid);
        $o = '';

        foreach ($orders as $m_order) { //$orders chua nhieu doi tuong. va` chi lay dc doi tuong con qua foreach
            $o = $m_order;
        }
        /* end */

        $html = '<div><b>DELIVERY DATE:' . Mage::getModel('core/date')->date('d-m-Y', strtotime(
                $o->getMwDeliverydateDate())) . ', ' . $o->getMwDeliverydateTime() . '</b></div><br/>';


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
            $s_address = $order->getShippingAddress();
        }

        //print contact Person
        $order_by_address = $b_address->getData('firstname') . ' ' . $b_address->getData('lastname');

        //print company
        $order_by_address .= '<br/>' . $b_address->getCompany();

        //print address
        $order_by_address .= '<br/>' . preg_replace("/[\n\r]/", ", ", $b_address->getData('street'));

        $countryName = Mage::getModel('directory/country')->load($b_address->getCountry())->getName();
        $order_by_address .= '<br/>' . $countryName . ' ' . $b_address->getData('postcode');

        //print telephone
        $order_by_address .= '<br/>' . $b_address->getData('telephone');

        //print email
        $order_by_address .= '<br/>' . $b_address->getData('email');


        //print shipping address
        //print contact Person
        $sent_to_address = $s_address->getData('firstname') . ' ' . $s_address->getData('lastname');

        //print company
        $sent_to_address .= '<br/>' . $s_address->getCompany();

        //print address
        $sent_to_address .= '<br/>' . preg_replace("/[\n\r]/", ", ", $s_address->getData('street'));

        $sent_countryName = Mage::getModel('directory/country')->load($s_address->getCountry())->getName();
        $sent_to_address .= '<br/>' . $sent_countryName . ' ' . $s_address->getData('postcode');

        //print telephone
        $sent_to_address .= '<br/>' . $s_address->getData('telephone');

        $html .= '<table>
                    <tr style="font-weight:bold;">
                        <td>ORDERED BY:</td>
                        <td>SENT TO:</td>
                    </tr>
                    <tr>
                        <td>' . $order_by_address . '</td>
                        <td>' . $sent_to_address . '</td>
                    </tr>
                </table>';
        $html .= '<div style="margin:10px 0; height:1px;"></div>';
        $html .= '<table style="border:solid 1px #666;">
                    <tr style="font-weight:bold;text-align:left;background-color:#999;">
                        <td style="border-right:solid 1px #666;width:30px;">No</td>
                        <td style="border-right:solid 1px #666;width:250px;">Gift Description</td>
                        <td style="border-right:solid 1px #666;width:100px;">Qty</td>
                        <td style="width:190px;">Amount</td>
                    </tr>';


        /* Add body */
        $num = 1;
        $collection = Mage::getModel('freegift/rule')->getCollection();
        foreach ($order->getAllItems() as $item) {
            $is_gift = false;
            foreach ($collection as $rule) {
                # code...
                if (strpos($rule['gift_product_ids'], $item->getProductId()) !== false && $rule['is_active'] == 1)
                    $is_gift = true;
            }

            $html .= '';
            if ($item->getParentItem()) {
                $product = Mage::getModel('catalog/product')->load($item->getProductId());
                if ($product->getAdditional() != 1) {
                    $html .= '<tr>
                            <td style="border-right:solid 1px #666;"></td>
                            <td style="border-right:solid 1px #666;color:red;">' . $item->getName() . '</td>
                            <td style="border-right:solid 1px #666;"></td>
                            <td></td>
                        </tr>';

                    continue;
                }
            } else if ($item->getProductType() != 'simple' || $is_gift == true) {
                $html .= '<tr><td style="border-right:solid 1px #666;text-align:center;">' . $num++ . '</td>';

                if ($item->getProductType() == 'simple') {
                    $html .= '<td style="border-right:solid 1px #666;"><font color="red">' . $item->getName() . '</font>';
                } else
                    $html .= '<td style="border-right:solid 1px #666;">' . $item->getName();

                $options = $this->getItemOptions($item);
                if ($options) {
                    foreach ($options as $option) {
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
                    }
                }

                $html .= '</td>';
                $html .= '<td style="border-right:solid 1px #666;text-align:center;">' . ($item->getQtyOrdered() * 1) . '</td>';
                $html .= '<td style="text-align:right;">' . $order->formatPriceTxt($item->getRowTotal()) . '</td></tr>';
            }
        }

        $html .= '<tr>
                <td style="border-top:solid 1px #666;border-right:solid 1px #666;text-align:center;"></td>
                <td style="border-top:solid 1px #666;border-right:solid 1px #666;text-align:center;"></td>
                <td style="border-top:solid 1px #666;border-right:solid 1px #666;text-align:center;">Subtotal (w/gst)</td>
                <td style="border-top:solid 1px #666;text-align:right;">' . $order->formatPriceTxt($order->getSubtotalInclTax()) . '</td>
                </tr>';

        if ($order->getTaxAmount() > 0) {
            $html .= '<tr>
                <td style="border-right:solid 1px #666;text-align:center;"></td>
                <td style="border-right:solid 1px #666;text-align:center;"></td>
                <td style="border-right:solid 1px #666;text-align:center;">Tax</td>
                <td style="text-align:right;">' . $order->formatPriceTxt($order->getTaxAmount()) . '</td>
                </tr>';
        }

        if ($order->getDiscountAmount() != 0) {
            $html .= '<tr>
                <td style="border-right:solid 1px #666;text-align:center;"></td>
                <td style="border-right:solid 1px #666;text-align:center;"></td>
                <td style="border-right:solid 1px #666;text-align:center;">Discount</td>
                <td style="text-align:right;">' . $order->formatPriceTxt($order->getDiscountAmount()) . '</td>
                </tr>';
        }

        if ($order->getShippingAmount() > 0) {
            $html .= '<tr>
                <td style="border-right:solid 1px #666;text-align:center;"></td>
                <td style="border-right:solid 1px #666;text-align:center;"></td>
                <td style="border-right:solid 1px #666;text-align:center;">Delivery</td>
                <td style="text-align:right;">' . $order->formatPriceTxt($order->getShippingInclTax()) . '</td>
                </tr>';
        }

        $html .= '<tr>
                <td style="border-right:solid 1px #666;text-align:center;"></td>
                <td style="border-right:solid 1px #666;text-align:center;"></td>
                <td style="border-right:solid 1px #666;text-align:center;">Grand total</td>
                <td style="text-align:right;">' . $order->formatPriceTxt($order->getGrandTotal()) . '</td>
                </tr>';

        $html .= '</table>';
        $html .= '<div style="margin:10px 0; height:1px;"></div>';
        $html .= '<table>
                        <tr>
                            <td colspan="5" style="text-lign:left;font-weight:bold;border-bottom:solid 4px #999;">METHOD OF PAYMENT:</td>
                        </tr>
                        <tr style="font-weight:bold;font-size:30px;">
                            <td style="width:100px;">Payment mode</td>
                            <td style="width:140px;">Card No</td>
                            <td style="width:100px;">Expiry Date</td>
                            <td style="width:140px;">Card Holder Name</td>
                            <td style="width:100px;">Trx Ind</td>
                        </tr>';


        if ($p_info->getCcTypeName()) {
            $html .= '<tr>';
            $html .= '<td>' . $p_info->getCcTypeName() . '</td>';

            $html .= '<td>';
            if ($p_info->getInfo())
                $html .= sprintf('xxxx-xxxx-xxxx-%s', $p_info->getInfo()->getCcLast4());
            $html .= '</td>';

            $html .= '<td>';
            if ($p_info->getCcExpDate())
                $html .= $p_info->getInfo()->getCcExpMonth() . '/' . $p_info->getInfo()->getCcExpYear();
            $html .= '</td>';

            $html .= '<td>';
            if ($p_info->getInfo())
                $html .= $p_info->getInfo()->getCcOwner();
            $html .= '</td>';
            $html .= '<td></td>';
            $html .= '</tr>';
        } else {
            $html .= '<tr><td colspan="5">';
            foreach ($payment as $value) {
                if (trim($value) != '') {
                    //Printing "Payment Method" lines
                    $value = preg_replace('/<br[^>]*>/i', "\n", $value);
                    foreach (Mage::helper('core/string')->str_split($value, 45, true, true) as $_value) {
                        $html .= strip_tags(trim($_value));
                    }
                }
            }
            $html .= '</td></tr>';
        }

        $_totalData = $order->getData();
        $html .= '  <tr>
                            <td colspan="5" style="text-align:right;font-weight:bold;">Total Amount paid: ' . $order->formatPriceTxt($_totalData['grand_total']) . '</td>
                        </tr>
                   </table>';

        $pdf->writeHTML($html, true, false, true, false, '');
    }

    private function insertHtmlTotals(&$pdf, $source)
    {

    }

    public function getItemOptions($item)
    {
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

}