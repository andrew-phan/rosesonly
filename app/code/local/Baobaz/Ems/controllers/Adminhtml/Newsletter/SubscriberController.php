<?php
$defController = Mage::getBaseDir()
    . DS . 'app' . DS . 'code' . DS . 'core'
    . DS . 'Mage' . DS . 'Adminhtml' . DS . 'controllers'
    . DS . 'Newsletter' . DS . 'SubscriberController.php';
require_once $defController;

class Baobaz_Ems_Adminhtml_Newsletter_SubscriberController extends Mage_Adminhtml_Newsletter_SubscriberController
{
    public function exportCsvUnsubAction()
    {
        $fileName   = 'subscribers.csv';
        $content    = $this->getLayout()->createBlock('adminhtml/newsletter_subscriber_grid')
            ->getCsvFileUnsub();

        $this->_prepareDownloadResponse($fileName, $content);

        // run batch upload to cheetahmail
        Mage::helper('baobaz_ems')->_login();
        Mage::getModel('baobaz_ems/webservice_subscribers')->Ems_Subscribers_Batch_Upload();

    }
}