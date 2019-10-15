<?php
/**
 * @category   Baobaz
 * @package    Baobaz_Ems
 * @copyright  Copyright (c) 2010 Baobaz (http://www.baobaz.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Baobaz EMS Observer
 *
 * Event handlers
 */
class GentoTech_CheetahMail_Model_Observer
{


    /*
    * Update newsletter subscriptions from cheetahmail to system.
    */
    public function update_subscriptions()
    {
        //require_once '../app/Mage.php';
        //Mage::app();

        $resource = Mage::getSingleton('core/resource');
        $db = $resource->getConnection('core_write');

        $baseDir = Mage::getBaseDir();
        $varDir = $baseDir . DS . 'var';
        $timeofImport = date('Ymd');
        $importDir = $varDir . DS . 'import/cheetahmail';

        // file from cheetah mail
        $_fileToImportRemote = '/fromcheetah/subs_'.$timeofImport.'.dat.txt';

        // file local
        //$_fileToImportBaseName = 'subs_'.$timeofImport.'.dat.txt';
        $_fileToImportBaseName = 'update_subscriptions.dat.txt';
        $_fileToImportLocal = $importDir . DS . $_fileToImportBaseName;


        $file = new Varien_Io_File();
        $file->mkdir($importDir);

        /* Connect to SFTP */
        $fileSftp = new Varien_Io_Sftp();
        try
        {
            $fileSftp->open(
                array(
                    'host' => 'tt.cheetahmail.com',
                    'username' => 'rosesonly',
                    'password' => 'P9NuV9tx'
                    //'timeout' => '10'
                )
            );

            $_fileToImportRemoteTmp = $fileSftp->read($_fileToImportRemote);

            $file->write($_fileToImportLocal, $_fileToImportRemoteTmp);

            $fileSftp->close();

        } catch (Exception $e) {
            echo $e->getMessage();
        }

        /* Read file from local */
        //$file->streamOpen($_fileToImportLocal, 'r');
        $fileText = $file->read($_fileToImportLocal);

        $array = explode("\n", $fileText);

        for ($i = 1; $i < count($array); $i ++){

            $temp[$i] = explode(',', $array[$i]);
            $email = $temp[$i][2];
            //$today = date("YmdHis");
            $date_sub = $temp[$i][3];
            $date_sub = date_create_from_format('YmdHis', $date_sub);
            $date = date_format($date_sub, 'Y-m-d H:i:s');

            $subscriber = Mage::getModel('newsletter/subscriber');
            $subscriber->setWebsiteId(Mage::app()->getWebsite()->getId());
            $subscriber->loadByEmail($email);
            //$subscriber->setStoreId(1);
            //$subscriber->setCustomerId(0);
            if($subscriber->getId()){

                //if ($subscriber->loadByEmail($email)){
                $query = 'Update `newsletter_subscriber` set `change_status_at`="'.$date.'", `subscriber_status`=1 where subscriber_email="'.$email.'" ';
                $db->query($query);
                // }else{

                //}
            }else{
                $query = 'Insert into `newsletter_subscriber` (`change_status_at`,`subscriber_email`,`subscriber_status`) values ("'.$date.'","'.$email.'",1)';
                //$query->setStoreId(Mage::app()->getStore()->getId());
                $db->query($query);
            }
            //}
        }
    }

    /*
    * Update newsletter unsubscriptions from cheetahmail to system.
    */
    public function update_unsubscriptions()
    {
        //require_once '../app/Mage.php';
        //Mage::app();

        $resource = Mage::getSingleton('core/resource');
        $db = $resource->getConnection('core_write');


        $baseDir = Mage::getBaseDir();
        $varDir = $baseDir . DS . 'var';
        $timeofImport = date('Ymd');
        $importDir = $varDir . DS . 'import/cheetahmail';

        // file from cheetah mail
        $_fileToImportRemote = '/fromcheetah/unsubs_'.$timeofImport.'.dat.txt';

        // file local
        //$_fileToImportBaseName = 'unsubs_'.$timeofImport.'.dat.txt';
        $_fileToImportBaseName = 'update_unsubscriptions.dat.txt';
        $_fileToImportLocal = $importDir . DS . $_fileToImportBaseName;

        /* Connect to SFTP */
        $file = new Varien_Io_File();
        $file->mkdir($importDir);


        $fileSftp = new Varien_Io_Sftp();
        try
        {
            $fileSftp->open(
                array(
                    'host' => 'tt.cheetahmail.com',
                    'username' => 'rosesonly',
                    'password' => 'P9NuV9tx'
                    //'timeout' => '10'
                )
            );
            $_fileToImportRemoteTmp = $fileSftp->read($_fileToImportRemote);

            $file->write($_fileToImportLocal, $_fileToImportRemoteTmp);


        } catch (Exception $e) {
            echo $e->getMessage();
        }

        //$file->open(array('path' => $importDir));
        //$file->streamOpen($_fileToImportBaseName, 'r');
        $fileText = $file->read($_fileToImportLocal);

        $array = explode("\n", $fileText);

        //echo'<pre>'; print_r($array); echo'</pre>';

        for ($i = 1; $i < count($array); $i ++){

            $temp[$i] = explode(',', $array[$i]);
            $email = $temp[$i][3];

            //$today = date("YmdHis");var_dump($today);die;
            $date_unsub = $temp[$i][4];
            $date_unsub = date_create_from_format('YmdHis', $date_unsub);
            $date = date_format($date_unsub, 'Y-m-d H:i:s');

            $subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($email);
            if($subscriber->getId())
            {
                $query = 'Update `newsletter_subscriber` set `change_status_at`="'.$date.'", `subscriber_status`=3 where subscriber_email="'.$email.'" ';
                $db->query($query);
            }else{
                $query = 'Insert into `newsletter_subscriber` (`change_status_at`,`subscriber_email`,`subscriber_status`) values ("'.$date.'","'.$email.'",3)';
                $db->query($query);
            }


        }
    }

    /*
    * Update newsletter change of address from cheetahmail to system.
    */

    public function update_coa()
    {
        /*require_once '../app/Mage.php';
        Mage::app();*/

        $resource = Mage::getSingleton('core/resource');
        $db = $resource->getConnection('core_write');

        $baseDir = Mage::getBaseDir();
        $varDir = $baseDir . DS . 'var';
        $timeofImport = date('Ymd');
        $importDir = $varDir . DS . 'import/cheetahmail';

        // file from cheetah mail
        $_fileToImportRemote = '/fromcheetah/coa_'.$timeofImport.'.dat.txt';

        // file local
        //$_fileToImportBaseName = 'coa_'.$timeofImport.'.dat.txt';
        $_fileToImportBaseName = 'update_coa.dat.txt';
        $_fileToImportLocal = $importDir . DS . $_fileToImportBaseName;

        $file = new Varien_Io_File();
        $file->mkdir($importDir);

        /* Connect to SFTP */
        $fileSftp = new Varien_Io_Sftp();
        try
        {
            $fileSftp->open(
                array(
                    'host' => 'tt.cheetahmail.com',
                    'username' => 'rosesonly',
                    'password' => 'P9NuV9tx'
                    //'timeout' => '10'
                )
            );

            $_fileToImportRemoteTmp = $fileSftp->read($_fileToImportRemote);

            $file->write($_fileToImportLocal, $_fileToImportRemoteTmp);

        } catch (Exception $e) {
            echo $e->getMessage();
        }

        /* Read file from local */
        $fileText = $file->read($_fileToImportLocal);

        $array = explode("\n", $fileText);

        /*echo'<pre>'; print_r($array); echo'<pre>';*/

        for ($i = 1; $i < count($array); $i ++){

            $temp[$i] = explode(',', $array[$i]);
            $email_from = $temp[$i][0];
            $email_to = $temp[$i][1];


            //$today = date("YmdHis");var_dump($today);die;
            $date_changed = $temp[$i][2];

            $date_changed = date_create_from_format('YmdHis', $date_changed);
            $date = date_format($date_changed, 'Y-m-d H:i:s');


            $subscriber = Mage::getModel('newsletter/subscriber');
            $subscriber->setWebsiteId(Mage::app()->getWebsite()->getId());
            $subscriber->loadByEmail($email_from);
            //$subscriber->setStoreId(1);
            //$subscriber->setCustomerId(0);
            if($subscriber->getId()){

                //if ($subscriber->loadByEmail($email)){
                $query = 'Update `newsletter_subscriber` set `change_status_at`="'.$date.'", `subscriber_email`="'.$email_to.'" where subscriber_email="'.$email_from.'" ';
                $db->query($query);
                // }else{

                //}
            }else{
                //$query = 'Insert into `newsletter_subscriber` (`change_status_at`,`subscriber_email`,`subscriber_status`) values ("'.$date.'","'.$email.'",1)';
                //$query->setStoreId(Mage::app()->getStore()->getId());
                //$db->query($query);
            }
            //}
        }
    }
}