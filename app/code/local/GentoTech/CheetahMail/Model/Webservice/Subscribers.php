<?php
/**
 * @category   Baobaz
 * @package    Baobaz_Ems
 * @copyright  Copyright (c) 2010 Baobaz (http://www.baobaz.com)
 * @author     Arnaud Ligny <arnaud.ligny@baobaz.com>
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Baobaz Ems Webservice Subscribers Model
 */
class GentoTech_CheetahMail_Model_Webservice_Subscribers
    extends Varien_Object
{

    public function getCookie(){

        $name = 'fef_roa_cm_api';
        $value = 'Cheetah@129';
        $period = 28800;

        Mage::getModel('core/cookie')->set($name, $value, $period);

        $cookie = Mage::getModel('core/cookie')->get($name);

        return $cookie;

    }
    public function getCookieLogin() {

        /*$name = 'fef_roa_cm_api';
        $value = 'Cheetah@129';
        $period = 28800;
        Mage::getSingleton('core/cookie')->set($name, $value, $period);

        $cookie = Mage::getSingleton('core/cookie')->get($name);*/

        /*var_dump($cookie);
        echo '<pre>';print_r($cookie); echo '</pre>';*/

        $cookie = $this->getCookie();
        if (empty($cookie)){
            $cookie = Mage::helper('baobaz_ems')->_login();//var_dump($cookie);
        }elseif ($cookie = 'err:auth'){
            $cookie = Mage::helper('baobaz_ems')->_login();
        }
        return $cookie;
    }

    /*
     *  Login
    */
    public  function _login()
    {
        $url = 'https://ebm.cheetahmail.com/api/login1?name=fef_roa_cm_api&cleartext=Cheetah@129';

        //open connection
        $handle = curl_init();

        // $cookie = Mage::getModel('baobaz_ems/webservice_subscribers')->getCookie();

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
        $result = false;
        // if request did not fail.
        if ($response !== false){

            //if request was ok, check response code
            $statusCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);

            if ($statusCode == 200){
                $result = true;
            }else{
                Mage::throwException('Cannot log in CheetahMail Server.');
            }
        }

        // close
        curl_close($handle);
        return $result;

    }

    /**
     *  Add EMS subscriber by email
     */
    public function Ems_Subscribers_Add($aid, $email, $sub = null, $unsub = null, $fname = '', $lname = ''){

        try{

            $url = 'https://ebm.cheetahmail.com/api/setuser1?aid=' . $aid . '&email=' . $email . '&sub='. $sub . '&unsub=' . $unsub . '&FNAME=' . $fname . '&LNAME=' . $lname;

            //open connection
            $handle = curl_init();
            curl_setopt($handle, CURLOPT_URL, $url);
//            curl_setopt($handle, CURLOPT_POST, true);
            curl_setopt($handle, CURLOPT_POST, false);
            curl_setopt($handle, CURLOPT_SSL_VERIFYPEER ,false);
            curl_setopt($handle, CURLOPT_SSL_VERIFYHOST,false);
//            curl_setopt($handle, CURLOPT_COOKIE, $cookies);

            curl_setopt($handle, CURLOPT_COOKIEJAR, Mage::getBaseDir('var') . DS . 'log' . DS . 'cookie.txt');
            curl_setopt($handle, CURLOPT_COOKIEFILE, Mage::getBaseDir('var') . DS . 'log' . DS . 'cookie.txt');

            //execute
            $response = curl_exec($handle);

            /*$result = false;
            if ($response !== false){
                $statusCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
                if ($statusCode == 200){
                    $result = true;
                }else{
                    Mage::throwException('Cannot log in CheetahMail Server.');
                }
            }*/

            // close
            curl_close($handle);

            //return $result;

        } catch (Exception $e) {
            Mage::logException($e);
            return false;
        }

    }


    /*
     * batch Upload file to cheetah mail
     */
    public function Ems_Subscribers_Batch_Upload(){

        try{

            $url = 'https://app.cheetahmail.com/cgi-bin/api/unsub1';

            // The full path to the file you want to send.
            //$filename = Mage::getBaseDir('var') . DS . 'subscribers' . DS . 'subscribers.csv';
            $filename = Mage::getBaseDir('var') . DS . 'export' . DS . 'subscribers.csv';

            $postData = array('file' => '@'.$filename, 'aid' => 2095840058, 'sid' => 2095847118, 'email_col' => 1, 'email' => 'cbhagnani@cheetahmail.com');
            //open connection
            $handle = curl_init();

            curl_setopt($handle, CURLOPT_URL, $url);
            curl_setopt($handle, CURLOPT_POST, true);
            curl_setopt($handle, CURLOPT_SSL_VERIFYPEER ,false);
            curl_setopt($handle, CURLOPT_SSL_VERIFYHOST,false);

            curl_setopt($handle, CURLOPT_COOKIEJAR, Mage::getBaseDir('var') . DS . 'log' . DS . 'cookie.txt');
            curl_setopt($handle, CURLOPT_COOKIEFILE, Mage::getBaseDir('var') . DS . 'log' . DS . 'cookie.txt');

            curl_setopt($handle, CURLOPT_POSTFIELDS, $postData);
            curl_setopt($handle, CURLOPT_RETURNTRANSFER,1);
            //execute
            //var_dump($url);die;
            $response = curl_exec($handle);
            //var_dump($response);

            /*$result = false;
            if ($response !== false){
                $statusCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
                if ($statusCode == 200){
                    $result = true;
                }else{
                    Mage::throwException('Cannot log in CheetahMail Server.');
                }
            }*/

            // close
            curl_close($handle);

            //return $result;

        } catch (Exception $e) {
            Mage::logException($e);
            return false;
        }
    }


    public function Track_To_Purchase($orderid, $amount, $items, $currency = null, $country = null)
    {
        try{

            //header("Content-Type: image/gif");

            $url = 'https://e.rosesonly.com.sg/a/r2095840058/rosesonlyasia.gif?a=' . $orderid . '&b=' . $amount . '&items='. $items . '&d=' . $currency . '&e=' . $country;
            var_dump($url);
            //open connection
            $handle = curl_init();

            curl_setopt($handle, CURLOPT_URL, $url);
//            curl_setopt($handle, CURLOPT_POST, true);
            curl_setopt($handle, CURLOPT_POST, false);
            curl_setopt($handle, CURLOPT_SSL_VERIFYPEER ,false);
            curl_setopt($handle, CURLOPT_SSL_VERIFYHOST,false);
            //curl_setopt($handle, CURLOPT_HEADER, 0);

//            curl_setopt($handle, CURLOPT_COOKIE, $cookies);

            /*curl_setopt($handle, CURLOPT_COOKIEJAR, Mage::getBaseDir('var') . DS . 'log' . DS . 'cookie.txt');
            curl_setopt($handle, CURLOPT_COOKIEFILE, Mage::getBaseDir('var') . DS . 'log' . DS . 'cookie.txt');*/

            //execute
            $response = curl_exec($handle);
            var_dump($response);
            $result = false;
            if ($response !== false){
                $statusCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
                if ($statusCode == 200){echo "success";
                    $result = true;
                }else{
                    Mage::throwException('Cannot log in CheetahMail Server.');
                }
            }

            // close
            curl_close($handle);

            return $result;

        } catch (Exception $e) {
            Mage::logException($e);
            return false;
        }
    }

}