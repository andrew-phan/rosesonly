<?php
class Baobaz_Ems_Block_Adminhtml_Newsletter_Subscriber_Grid extends Mage_Adminhtml_Block_Newsletter_Subscriber_Grid
{
    const ENCLOSURE = '"';
    const DELIMITER = ',';

    protected $_list = null;
    public function __construct()
    {
        parent::__construct();

        $collection = Mage::getResourceModel('newsletter/subscriber_collection');
        $collection
            //->showCustomerInfo()
            ->addFieldToSelect('subscriber_email')

            //->showCustomerInfo2(true)
            ->addFieldToSelect('subscriber_status')
            ->addFieldToFilter('subscriber_status', 3);

            //->showCustomerGender() // we added this
            //->addSubscriberTypeField();
            //->showStoreInfo();

        $this->setlist($collection);
    }


    public function setlist($collection){
        $this->_list = $collection;
    }

    protected function _prepareColumns()
    {
        $this->addExportType('*/*/exportCsvUnsub', 'CSV_Unsub');
        return parent::_prepareColumns();
    }

    protected function _getCsvHeaders($newsletters)
    {
        $newsletter = current($newsletters);
        $headers = array_keys($newsletter->getData());

        return $headers;
    }

    /**
     * Retrieve a file container array by grid data as CSV
     *
     * Return array with keys type and value
     *
     * @return array
     */
    public function getCsvFileUnsub()
    {
        $this->_isExport = true;
        $this->_prepareGrid();

        if (!is_null($this->_list)) {
            $items = $this->_list->getItems();
            if (count($items) > 0) {

                $io = new Varien_Io_File();

                $path = Mage::getBaseDir('var') . DS . 'export' . DS;
                //$name = md5(microtime());

                $io->mkdir($path);

                $name = 'subscribers';
                $file = $path . DS . $name . '.csv';

                $io->setAllowCreateFolders(true);
                $io->open(array('path' => $path));
                $io->streamOpen($file, 'w+');
                $io->streamLock(true);
                //$io->streamWriteCsv($this->_getExportHeaders());
                $io->streamWriteCsv($this->_getCsvHeaders($items));

                foreach ($items as $newsletter) {
                    $temp = array_values($newsletter->getData());
                    $temp[1]='Unsubscribed';
                    //print_r($temp);return;
                    $io->streamWriteCsv($temp);
                }

                //$this->writeHeadRow($io);

                //$this->_exportIterateCollection('_exportCsvItem', array($io));

                if ($this->getCountTotals()) {
                    $io->streamWriteCsv($this->_getExportTotals());
                }

                $io->streamUnlock();
                $io->streamClose();

                return array(
                    'type'  => 'filename',
                    'value' => $file,
                    'rm'    => false // can delete file after use
                );

            }
        }
    }

    /**
     * Writes the head row with the column names in the csv file.
     *
     * @param $fp The file handle of the csv file
     */
    protected function writeHeadRow($fp)
    {
        //fputcsv($fp, $this->getHeadRowValues(), self::DELIMITER, self::ENCLOSURE);
        $this->getHeadRowValues();
    }

    /**
     * Returns the head column names.
     *
     * @return Array The array containing all column names
     */
    protected function getHeadRowValues()
    {
        return array(
            'Email',
            'Customer First Name',
            'Customer Last Name',
            'Status'
        );
    }
}