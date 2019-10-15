<?php
/*
 *  Created on AUG 30, 2012
 *  Author Ivan Proskuryakov  - Magazento.com
 *  Copyright Proskuryakov Ivan. Magazento.com © 2012. All Rights Reserved.
 *  Single Use, Limited Licence and Single Use No Resale Licence ["Single Use"]
 */
?>
<?php class Magazento_Pdfinvoice_Helper_Data extends Mage_Core_Helper_Abstract {
	
    public function versionUseAdminTitle() {
        $info = explode('.', Mage::getVersion());
        if ($info[0] > 1) {
            return true;
        }
        if ($info[1] > 3) {
            return true;
        }
        return false;
    }

    public function versionUseWysiwig() {
        $info = explode('.', Mage::getVersion());
        if ($info[0] > 1) {
            return true;
        }
        if ($info[1] > 3) {
            return true;
        }
        return false;
    }
    
    public function getPdfTypeByController() {
       $action = Mage::app()->getRequest()->getControllerName();
       switch ($action) {
           case 'admin_invoice':
               $return = 'invoice';
               break;
           case 'admin_shipment':
               $return = 'shipment';
               break;
           case 'admin_creditmemo':
               $return = 'creditmemo';
               break;
           default:
               break;
       }
       return $return;
    }

    public function numberArray($max,$text) {

        $items = array();
        for ($index = 1; $index <= $max; $index++) {
            $items[$index]=$text.' '.$index;
        }
        return $items;
    }

    
    
    
    public function pdfInvoiceTemplateParce($invoice,$content) {
        
        $vars = $this->__parse($content);

        foreach ($vars as $key => $params){
            
            if ($params[1] == 'invoice') {
                $value = $invoice->getData($params[2]);
                if (is_numeric($value)) {
                   $value = number_format($value, 0, '.', '');
                }
                $content = str_replace($key, $value, $content);
            }
        }
        return $content;
    }    
    public function pdfOrderTemplateParce($order,$content) {
        
        $vars = $this->__parse($content);

        foreach ($vars as $key => $params){
            
            if ($params[1] == 'order') {
                $value = $order->getData($params[2]);
                if (is_numeric($value)) {
                   $value = number_format($value, 2, '.', '');
                }
                $content = str_replace($key, $value, $content);
            }
        }
        return $content;
    }    
    
    public function pdfGeneralTemplateParce($block,$content) {
        $vars = $this->__parse($content);

        foreach ($vars as $key => $params){
            
            if (($params[1] == 'general')) {
                $value = $params[2];
                
                switch ($value) {
                    case 'billing_adress':
                        $block->setTemplate('magazento_pdfinvoice/sales/order/print/invoice/billing_adress.phtml');
                        $value = $block->toHtml();
                        break;
                    case 'shipping_adress':
                        $block->setTemplate('magazento_pdfinvoice/sales/order/print/invoice/shipping_adress.phtml');
                        $value = $block->toHtml();
                        break;
                    case 'order_items':
                        $block->setTemplate('magazento_pdfinvoice/sales/order/print/invoice/order_items.phtml');
                        $value = $block->toHtml();
                        break;
                    case 'payment_info':
                        $block->setTemplate('magazento_pdfinvoice/sales/order/print/invoice/payment_info.phtml');
                        $value = $block->toHtml();
                        break;
                    default:
                        $value = null;
                }     
                if ($value != null) {
                    $content = str_replace($key, $value, $content);    
                } 
            }
        }
        return $content;
    }    

    public function __parse($template)
    {
        $vars = array();
        preg_match_all('~(\{{(.*?)\}})~', $template, $matches, PREG_SET_ORDER);
        foreach ($matches as $match){
            $var = explode(".",$match[2]);
            $vars[$match[1]]= $var;
//                var_dump($vars);
//                exit();
        }
        return $vars;
    }      
    
    
}
