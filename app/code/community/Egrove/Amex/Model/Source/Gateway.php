<?php

class Egrove_Amex_Model_Source_Gateway
{
    
    public function toOptionArray()
    {
        return
        array(
             array("value"=>'ssl',"label"=> 'Auth-Purchase with 3DS Authentication'),
             array("value"=>'threeDSecure',"label"=> '3DS Authentication Only'),
        );
    }
    

}