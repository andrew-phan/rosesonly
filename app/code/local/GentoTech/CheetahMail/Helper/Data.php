<?php
/**
 * @category   Baobaz
 * @package    Baobaz_Ems
 * @copyright  Copyright (c) 2010 Baobaz (http://www.baobaz.com)
 * @author     Arnaud Ligny <arnaud.ligny@baobaz.com>
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Baobaz Ems default Helper
 */
class GentoTech_CheetahMail_Helper_Data extends Mage_Core_Helper_Abstract
{


    /*
     *  Login
    */
    public function _login()
    {
        $url = 'https://ebm.cheetahmail.com/api/login1?name=fef_roa_cm_api&cleartext=Cheetah@129';

        //open connection
        $handle = curl_init();

        curl_setopt($handle, CURLOPT_URL, $url);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER ,false);
        curl_setopt($handle, CURLOPT_SSL_VERIFYHOST,false);
        curl_setopt($handle, CURLOPT_POST, false);
        //curl_setopt($handle, CURLOPT_COOKIE, $cookie);
        curl_setopt($handle, CURLOPT_COOKIEJAR, Mage::getBaseDir('var') . DS . 'log' . DS . 'cookie.txt');
        curl_setopt($handle, CURLOPT_COOKIEFILE, Mage::getBaseDir('var') . DS . 'log' . DS . 'cookie.txt');
        /*curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($handle, CURLOPT_HEADER, 1);*/

        //execute
        $response = curl_exec($handle);

        preg_match('/^Set-Cookie:\s*([^;]*)/mi', $response, $m);

        parse_str($m[1], $cookies);
        // var_dump($cookies);
        /*var_dump(
            $response
        );*/
        //return;
        //var_dump($response);
        //$result = false;
        // if request did not fail.
        //if ($response !== false){

            //if request was ok, check response code
            //$statusCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);

           // if ($statusCode == 200){
                //$result = true;
            //}else{
              //  Mage::throwException('Cannot log in CheetahMail Server.');
           // }
       // }

        // close
        curl_close($handle);
        //return $result;

    }
}