<?php

class Ant_Latepayment_Block_Adminhtml_Latepayment_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public $daysleft;
    public $statuses;
    
    public function __construct() {
        parent::__construct();
        $this->setId("latepaymentGrid");
        $this->setDefaultSort("daysleft");
        $this->setDefaultDir("ASC");
        $this->setSaveParametersInSession(true);
    }

    /*protected function _prepareCollection() {
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        
        $query = "SELECT entity_id, g.status, g.store_id, customer_id, base_grand_total, grand_total, increment_id, g.base_currency_code, order_currency_code, 
                shipping_name, billing_name, g.created_at, c.mw_deliverydate_date, MAX(IFNULL(v.value, 0)) AS lead_time
                FROM sales_flat_order_grid g INNER JOIN sales_flat_order_item i on g.entity_id = i.order_id
                INNER JOIN mw_onestepcheckout c on c.sales_order_id = g.entity_id
                LEFT JOIN (SELECT entity_id AS product_id, value FROM catalog_product_entity_varchar 
                WHERE attribute_id = (SELECT attribute_id FROM eav_attribute WHERE attribute_code = 'lead_time')) v on v.product_id = i.product_id
            WHERE payment_validated = 0 AND g.status LIKE '%pending%'
            GROUP BY entity_id, g.status, g.store_id, customer_id, base_grand_total, grand_total, increment_id, g.base_currency_code, order_currency_code, 
		shipping_name, billing_name, g.created_at, c.mw_deliverydate_date";
        $result = $readConnection->query($query);

//        $attributeId = Mage::getResourceModel('eav/entity_attribute')->getAttribute_code('lead_time')->getId();
//        $collection = Mage::getResourceModel('sales/order_grid_collection');
//        $collection->getSelect()->join(array('item'=> 'sales_flat_order_item'),
//                'item.order_id = main_table.entity_id',
//                array('product_id'));

//        $collection = new Varien_Data_Collection($readConnection);
        $lateIds;

        while ($row = $result->fetch()) {
            $leadtime = $row['lead_time'];
            $deliverydate = $row['mw_deliverydate_date'];
            $day = substr($deliverydate, 3, 2);
            $mounth = substr($deliverydate, 0, 2);
            $year = substr($deliverydate, 6, 4);
            $deliverydays = mktime(0, 0, 0, $mounth, $day, $year);

            $days = Mage::getStoreConfig("latepayment/latepaymentsetting/latepaymentday");
            $day = $day - $leadtime;
            $this->date_fix_date($mounth, $day, $year);
            $max = mktime(0, 0, 0, $mounth, $day, $year); //Thoi gian lead time

            $day = $day - $days;
            $this->date_fix_date($mounth, $day, $year);
            $min = mktime(0, 0, 0, $mounth, $day, $year); //Thoi gian tre nhat

            $now = getdate();
            $cday = $now['mday'];
            $cmounth = $now['mon'];
            $cyear = $now['year'];
            $current = mktime(0, 0, 0, $cmounth, $cday, $cyear);  //Now

            if ($current > $min) {
                if ($current <= $max) {
                    $late = 'late';
                    $latedays = ($deliverydays - $current) / (60 * 60 * 24);
                } else {
                    $late = 'expired';
                    $latedays = '';
                }
                $lateIds[] = $row['entity_id'];
                $this->daysleft[$row['entity_id']] = $latedays;
                $this->statuses[$row['entity_id']] = $late;
                
//                $item = new Varien_Object();
//                $item->setId($row['entity_id']);
//                $item->setIncrement_id($row['increment_id']);
//                $item->setStore_id($row['store_id']);
//                $item->setCreated_at($row['created_at']);
//                $item->setBilling_name($row['billing_name']);
//                $item->setShipping_name($row['shipping_name']);
//                $item->setMw_deliverydate_date($row['mw_deliverydate_date']);
//                $item->setBase_currency_code($row['base_currency_code']);
//                $item->setBase_grand_total($row['base_grand_total']);
//                $item->setOrder_currency_code($row['order_currency_code']);
//                $item->setGrand_total($row['grand_total']);
//                $item->setStatus($row['status']);
//                $item->setLate($late);
//                $item->setLatedays($latedays);

//                $collection->addItem($item);
            }
        }
        echo count($lateIds);
        
        $collection = Mage::getResourceModel('sales/order_grid_collection')
                ->addAttributeToFilter('entity_id', array('in' => $lateIds));
        $collection->getSelect()->joinleft(array('one_step' => 'mw_onestepcheckout'), 
                    'one_step.sales_order_id = main_table.entity_id', 
                    array('mw_deliverydate_date'));
//        $collection->getSelect()->joinleft(array('latestatus' => $this->statuses), 
//                    'entity_id', 
//                    '*');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }*/

    protected function _prepareCollection(){
        $collection = Mage::getModel('latepayment/latepayment')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    protected function _prepareMassaction() {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('entity_id');
        $this->getMassactionBlock()->addItem('reminder', array(
            'label' => Mage::helper('tax')->__('Send Email Reminder'),
            'url' => $this->getUrl('*/*/reminder'),
            'confirm' => Mage::helper('tax')->__('Are you sure?')
        ));
        return $this;
    }

    protected function _prepareColumns() {
        $lateStatus['late'] = 'Late';
        $lateStatus['expired'] = 'Expired';
        
//        $this->addColumn("order_id", array(
//            "header" => Mage::helper("latepayment")->__("ID"),
//            "align" => "right",
//            "width" => "50px",
//            "index" => "order_id",
//        ));

        $this->addColumn('real_order_id', array(
            'header' => Mage::helper('sales')->__('Order #'),
            'width' => '80px',
            'type' => 'text',
            'index' => 'increment_id',
        ));

        $this->addColumn('store_id', array(
            'header' => Mage::helper('sales')->__('Purchased From (Store)'),
            'width' => '120px',
            'index' => 'store_id',
            'type' => 'store',
            'store_view' => true,
            'display_deleted' => true,
        ));

        $this->addColumn('created_at', array(
            'header' => Mage::helper('sales')->__('Purchased On'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '200px',
        ));

        $this->addColumn('billing_name', array(
            'header' => Mage::helper('sales')->__('Bill to Name'),
            'index' => 'billing_name',
        ));

        $this->addColumn('shipping_name', array(
            'header' => Mage::helper('sales')->__('Ship to Name'),
            'index' => 'shipping_name',
        ));

        $this->addColumn('mw_deliverydate_date', array(
            'header' => Mage::helper('sales')->__('Delivery date'),
            'index' => 'mw_deliverydate_date',
            'type' => 'date',
        ));

        $this->addColumn('base_grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Base)'),
            'index' => 'base_grand_total',
            'type' => 'currency',
            'currency' => 'base_currency_code',
        ));

        $this->addColumn('grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Purchased)'),
            'index' => 'grand_total',
            'type' => 'currency',
            'currency' => 'order_currency_code',
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('sales')->__('Status'),
            'index' => 'status',
            'type' => 'options',
            'width' => '70px',
            'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
        ));

        $this->addColumn('latestatus', array(
            'header' => Mage::helper('sales')->__('Late'),
            'index' => 'latestatus',
            'type' => 'options',
            'options' => $lateStatus,
            'width' => '70px',
            'align' => 'center',
        ));

        $this->addColumn('daysleft', array(
            'header' => Mage::helper('sales')->__('Days left'),
            'index' => 'daysleft',
            'type' => 'text',
            'align' => 'center',
            'width' => '70px',
        ));
        
        $this->addColumn('sendemail', array(
            'header' => Mage::helper('sales')->__('Send email'),
            'index' => 'sent',
            'type' => 'datetime',
            'align' => 'center',
            'width' => '70px',
        ));
        
        return parent::_prepareColumns();
    }

    public function getRowUrl($row) {
        return Mage::getModel('adminhtml/url')->getUrl('adminhtml/sales_order/view', array("order_id" => $row->getOrder_id()));
    }

    private function date_fix_date(&$month, &$day, &$year) {
        if ($month > 12) {
            while ($month > 12) {
                $month-=12; //subtract a $year
                $year++; //add a $year
            }
        } else if ($month < 1) {
            while ($month < 1) {
                $month +=12; //add a $year
                $year--; //subtract a $year
            }
        }
        if ($day > 31) {
            while ($day > 31) {
                if ($month == 2) {
                    if ($this->is_leap_year($year)) {//subtract a $month
                        $day-=29;
                    } else {
                        $day-=28;
                    }
                    $month++; //add a $month
                } else if ($this->date_hasThirtyOneDays($month)) {
                    $day-=31;
                    $month++;
                } else {
                    $day-=30;
                    $month++;
                }
            }//end while
            while ($month > 12) { //recheck $months
                $month-=12; //subtract a $year
                $year++; //add a $year
            }
        } else if ($day < 1) {
            while ($day < 1) {
                $month--; //subtract a $month
                if ($month == 2) {
                    if ($this->is_leap_year($year)) {//add a $month
                        $day+=29;
                    } else {
                        $day+=28;
                    }
                } else if ($this->date_hasThirtyOneDays($month)) {
                    $day+=31;
                } else {
                    $day+=30;
                }
            }//end while
            while ($month < 1) {//recheck $months
                $month+=12; //add a $year
                $year--; //subtract a $year
            }
        } else if ($month == 2) {
            if ($this->is_leap_year($year) && $day > 29) {
                $day-=29;
                $month++;
            } else if ($day > 28) {
                $day-=28;
                $month++;
            }
        } else if (!$this->date_hasThirtyOneDays($month) && $day > 30) {
            $day-=30;
            $month++;
        }
        if ($year < 1900)
            $year = 1900;
    }

    private function date_hasThirtyOneDays($month) {
        if ($month < 8)
            return $month % 2 == 1;
        else
            return $month % 2 == 0;
    }

    private function is_leap_year($year) {
        return (0 == $year % 4 && 0 != $year % 100 || 0 == $year % 400);
    }

}