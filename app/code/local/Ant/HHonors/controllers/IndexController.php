<?php

class Ant_HHonors_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        // check session hhonors
//        $session = Mage::getSingleton('core/session');
//        if(!$session->getData('hhonors'))
//            $session->setData('hhonors', array('setAt' => time()));
        
        // load content from home page and print to screen
//        $page = file_get_contents(Mage::getUrl('home'));
//        echo $page;
        //echo Mage::app()->getStore()->getCode();
        echo Mage::getUrl('hhonors_action/index/checkhhonors');
    }
    
    public function checkhhonorsAction()
    {
        $hhonors_number =$this->getRequest()->getPost("hhonors_account_number");
        $hhonors_bonus_code = $this->getRequest()->getPost("hhonors_bonus_code");
        
        
        // Hau added: need uncomment 
//         check valid hhonors code
        $hhonor_numer_8digit = substr($hhonors_number, 0, strlen($hhonors_number) - 1);
        $hhonors_number_int = intval($hhonor_numer_8digit);
        $tmp= $hhonors_number_int/9;
        $tmp= floor($tmp) * 9;
        $subtract_result = abs($hhonors_number_int - $tmp);
        $subtract_result+=1;
        if(strcmp(strval($subtract_result),$hhonors_number[strlen($hhonors_number)-1]) != 0)
        {
            echo 'false';
            return;
        }
//         end check valid hhonors code
        $session = Mage::getSingleton('core/session');
        $session->setData('hhonors_number',$hhonors_number);
        $session->setData('hhonors_bonus_code',$hhonors_bonus_code);

        $customer_collection = Mage::getModel('customer/customer')
                                ->getCollection()
                                ->addAttributeToSelect('name')
                                ->addAttributeToSelect('id')
                                ->addFieldToFilter('hhonors_number',$hhonors_number)
                                ->addFieldToFilter('hhonors_bonus_code', $hhonors_bonus_code);
        if(count($customer_collection) > 0)
        {
            foreach($customer_collection as $customer)
            {
                $session->setData('hhonors_customer_id',$customer->getId());
                break;
            }
        }
        
        echo 'true';
    }
}
