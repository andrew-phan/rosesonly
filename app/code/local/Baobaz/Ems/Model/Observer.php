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
class Baobaz_Ems_Model_Observer
{
    /**
     * Adds columns to Mage_Adminhtml_Block_Newsletter_Subscriber_Grid
     * 
     * @param Varien_Event_Observer $observer
     */
    public function beforeBlockToHtml(Varien_Event_Observer $observer)
    {
        $grid = $observer->getBlock();

        /**
         * Mage_Adminhtml_Block_Newsletter_Subscriber_Grid
         */
        if ($grid instanceof Mage_Adminhtml_Block_Newsletter_Subscriber_Grid) {
            // EMS Status
            $grid->addColumnAfter('ems_status', array(
                'header'    => Mage::helper('newsletter')->__('EMS Status' . $grid->getLastColumnId()),
                'index'     => 'ems_status',
                'width'     => '95px',
                'renderer'  => new Baobaz_Ems_Block_Adminhtml_Newsletter_Renderer_Status(),
                'filter'    => false,
                'sortable'  => false,
            ), 'store');
            // EMS Opt'in (if it is enabled)
            if (Baobaz_Ems_Model_Config::getOptin('field') != '') {
                $grid->addColumnAfter('ems_optin', array(
                    'header'    => Mage::helper('newsletter')->__('EMS Opt\'in'),
                    'index'     => 'ems_optin',
                    'renderer'  => new Baobaz_Ems_Block_Adminhtml_Newsletter_Renderer_Optin(),
                    'filter'    => false,
                    'sortable'  => false,
                ), 'ems_status');
            }
        }
    }

    /**
     * Observes "customer_save_after" event
     * to add customer to EMS via webservice
     *
     * @param Varien_Event_Observer $observer
     * @return Baobaz_Ems_Model_Observer
     */
    public function updateCustomer(Varien_Event_Observer $observer)
    {
        Mage::helper('baobaz_ems')->logDebug('Observer: ' . $observer->getEvent()->getName()); // debug

        $customer = $observer->getEvent()->getCustomer();
        $emsSubscribers = Mage::getModel('baobaz_ems/webservice_subscribers');
        /* @var $emsSubscribers Baobaz_Ems_Model_Webservice_Subscribers */

        // subscribe
        if (Mage::getModel('newsletter/subscriber')->loadByCustomer($customer)->isSubscribed()) {
            $emsSubscribers->subscribe($customer->getEmail());
        }
        // unsubscribe
        else {
            $emsSubscribers->unsubscribe($customer->getEmail());
        }

        // update EMS fields
        $data = Mage::getModel('baobaz_ems/adapter_customer')->load($customer)->toEms();
        Mage::helper('baobaz_ems')->logDebug(array('updateCustomer observer data' => $data)); // debug
        $emsSubscribers->update($customer->getEmail(), $data);

        return $this;
    }

    /**
     * Observes "customer_delete_before" event
     * to unsubscribe customer to EMS via webservice
     *
     * @param Varien_Event_Observer $observer
     * @return Baobaz_Ems_Model_Observer
     */
    public function deleteCustomer(Varien_Event_Observer $observer)
    {
        Mage::helper('baobaz_ems')->logDebug('Observer: ' . $observer->getEvent()->getName()); // debug

        $customer   = $observer->getEvent()->getCustomer();
        $emsSubscribers = Mage::getModel('baobaz_ems/webservice_subscribers');
        /* @var $emsSubscribers Baobaz_Ems_Model_Webservice_Subscribers */
        
        // unsubscribe
        $emsSubscribers->unsubscribe($customer->getEmail());

        return $this;
    }

    /**
     * Observes "newsletter_subscriber_save_before" event
     * to update subscriber change status date
     *
     * @param Varien_Event_Observer $observer
     * @return Baobaz_Ems_Model_Observer
     */
    public function updateSubscriberStatusDate(Varien_Event_Observer $observer)
    {
        Mage::helper('baobaz_ems')->logDebug('Observer: ' . $observer->getEvent()->getName()); // debug

        $subscriber = $observer->getEvent()->getSubscriber();
        // date (Zend_Date with default time zone)
        $date = Mage::app()->getLocale()->date(null, null, null, false);
        // set change status date
        $subscriber->setChangeStatusAt($date->toString('yyyy-MM-dd HH:mm:ss'));

        Mage::helper('baobaz_ems')->logDebug('Update date: ' . $date->toString('yyyy-MM-dd HH:mm:ss')); // debug

        return $this;
    }

    /**
     * Observes "newsletter_subscriber_save_after" event
     * to send subscriber to EMS via webservice
     *
     * @param Varien_Event_Observer $observer
     * @return Baobaz_Ems_Model_Observer
     */
    public function updateSubscriber(Varien_Event_Observer $observer)
    {
        Mage::helper('baobaz_ems')->logDebug('Observer: ' . $observer->getEvent()->getName()); // debug

        $login = Mage::helper('baobaz_ems')->_login();

        if ($login){

            $subscriber = $observer->getEvent()->getSubscriber();
            $emsSubscribers = Mage::getModel('baobaz_ems/webservice_subscribers');
            /* @var $emsSubscribers Baobaz_Ems_Model_Webservice_Subscribers */

            // guest
            if (!$subscriber->getCustomerId()) {
                // subscribe
                if ($subscriber->getSubscriberStatus() == '1') {
                    $emsSubscribers->subscribe($subscriber->getSubscriberEmail());
                }
                // unsubscribe
                else {
                    $emsSubscribers->unsubscribe($subscriber->getSubscriberEmail());
                }
            }
            // customer
            else {
                if ($subscriber->getSubscriberStatus() != '1') {
                    $emsSubscribers->unsubscribe($subscriber->getEmail());
                }
            }

            return $this;
        }

    }
    
    /**
     * Observes "newsletter_subscriber_delete_after" event
     * to unsubscribe subscriber to EMS via webservice
     * 
     * @param Varien_Event_Observer $observer
     */
    public function deleteSubscriber(Varien_Event_Observer $observer)
    {
        Mage::helper('baobaz_ems')->logDebug('Observer: ' . $observer->getEvent()->getName()); // debug

        $subscriber = $observer->getEvent()->getSubscriber();
        $emsSubscribers = Mage::getModel('baobaz_ems/webservice_subscribers');
        /* @var $emsSubscribers Baobaz_Ems_Model_Webservice_Subscribers */

        // guest
        if (!$subscriber->getCustomerId()) {
            $emsSubscribers->unsubscribe($subscriber->getSubscriberEmail());
        }
        // customer
        else {
            $emsSubscribers->unsubscribe($subscriber->getEmail());
        }

        return $this;
    }  

    /**
     * Scheduled actions (cron)
     * Asynchronous process
     *
     * @todo
     *  - update Magento customer datas to EMS database
     *  - update subscription status : check date beetween Magento & EMS
     *
     * @param Mage_Cron_Model_Schedule $schedule
     * @return Baobaz_Ems_Model_Observer
     */
    public function scheduledActions($schedule)
    {
        $date = Mage::app()->getLocale()->date(null, null, null, false);
        Mage::log('run at ' . $date->toString('yyyy-MM-dd HH:mm:ss'), Zend_Log::INFO, 'cron.ems.log');

        return $this;

        // Notes (in french) :
        // 1. on va consulter la base EMS pour connaitre le statut d'abonnement
        // de chacun des comptes utilisateurs présent dans la BDD
        // 2. si le compte est désabonné (côté EMS)
        // et que la date de désincription est supérieur à la date
        // de changement de statut dans Magento (change_status_at)
        // 3. alors on désinscrit le customer côté Magento

        // date format test
        //$date_test = new Zend_Date('31/12/2011 23:59:59', 'dd/MM/yyyy HH:mm:ss');
        //Mage::helper('baobaz_ems')->logDebug($date_test->toString('yyyy-MM-dd HH:mm:ss')); // debug
    }

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

        echo'<pre>'; print_r($array); echo'<pre>';

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