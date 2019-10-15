<?php
class Mage_Stockalertcron_Model_Cron{	
    public function checkOutOfStock(){
            Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID); 
            date_default_timezone_set('Asia/Singapore');
            $this->resetSendmailOfOfStock();
            $this->sendMailOutOfStock();
    } 
        
        private function resetSendmailOfOfStock()
    {
//        $stockCollection = Mage::getModel('cataloginventory/stock_item')->getCollection()
//        ->addFieldToFilter('is_in_stock', 1)
//        ->addFieldToFilter('is_sendmail_outofstock', 1);
            
        $stockCollection = Mage::getModel('cataloginventory/stock_item')->getCollection()
                                ->addFieldToFilter('is_sendmail_outofstock',1);
        foreach ($stockCollection as $stock) {
            $delta_time = time() - strtotime($stock->getData('lastime_sendmail_outofstock'));
            $hour = $delta_time / 3600;
            
            if($stock->getData('is_in_stock') === '1' || ($stock->getData('is_in_stock') === '0' && $hour > 1))
            {
                $stock->setData('is_sendmail_outofstock',0); 
                $stock->save();
            }
        }
        
    }
    
    private function sendMailOutOfStock()
    {
        $stockCollection = Mage::getModel('cataloginventory/stock_item')->getCollection()
        ->addFieldToFilter('is_in_stock', 0)
        ->addFieldToFilter('is_sendmail_outofstock', 0);
        
        $productIds = array();

        foreach ($stockCollection as $item) {
            $productIds[] = $item->getOrigData('product_id');
        }

        $productCollection = Mage::getModel('catalog/product')->getCollection()
            ->addIdFilter($productIds)
            ->addAttributeToSelect('url_key')
            ->addAttributeToSelect('name');
        
        $productsName = array();
        $productsUrl = array(); 
        
        foreach ($productCollection as $product) {
            //if($product->getTypeId() == 'simple'){
                $productsName[] = $product->getData('name');
                $productsUrl[] = $product->getProductUrl();    
            //}            
        }  
        
        
        if(count($productsName) > 0){
            // just send mail when have new out of stock product
            $this->sendEmailToAdmin($productsName, $productsUrl);
        }
        
        foreach ($stockCollection as $stock) {
            $stock->setData('is_sendmail_outofstock',1); 
            $stock->setData('lastime_sendmail_outofstock',time());
            $stock->save();
        }
        
        Mage::log('Checked out of stock at '.date('H:i:s m-d-Y'));
    }
    
    private function sendEmailToAdmin($productsName, $productsUrl)
    {
        $admin_email = Mage::getStoreConfig('trans_email/ident_general/email'); //fetch sender email Admin
        $admin_name = Mage::getStoreConfig('trans_email/ident_general/name'); //fetch sender name Admin
        $mail = Mage::getModel('core/email');
        $mail->setToName($admin_name);
        //$mail->setToEmail($admin_email);
        $mail->setToEmail('hoang.dinh21@gmail.com');

        $mail->setSubject('List of out of stock product at '. date("H:i:s m-d-Y"));
        $mail->setFromEmail($admin_email);
        $mail->setFromName($admin_name);
        $mail->setType('html');// You can use Html or text as Mail format
        
        $body = "List of out of stock product at ".date("H:i:s m-d-Y")."<br/><br/>";
        
        for($i=0;$i < count($productsName); $i++)
        {
            $body.="<a href='".$productsUrl[$i]."' target=_blank />".$productsName[$i]."</a><br/>";
        }
        $mail->setBody($body);
        
        try {
            $mail->send();
            $mail->setToEmail('hoang.dinh21@gmail.com');
            $mail->send();
            Mage::getSingleton('core/session')->addSuccess('Your request has been sent');
        }
            catch (Exception $e) {
            Mage::getSingleton('core/session')->addError('Unable to send.');
        }
    }
}