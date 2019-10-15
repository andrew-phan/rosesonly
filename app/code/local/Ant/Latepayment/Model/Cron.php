<?php

class Ant_Latepayment_Model_Cron {

    public function getLatepayment() {

        /* get Late Payment */
        //do something
        $paymentmethod = Mage::getStoreConfig("latepayment/latepaymentsetting/payment_method");
        $paymentarray = explode(',', $paymentmethod);
        $method = str_replace(",", "','", $paymentmethod);
        $method = "'" . $method . "'";
        $paymentorders = Mage::getModel('sales/order_payment')->getCollection()
                ->addFieldToFilter('method', array('in' => $paymentarray))
                ->addAttributeToSelect('entity_id');
        foreach ($paymentorders as $paymentorder) {
            $paymentordersId[] = $paymentorder->getId();
        }
        //Delete not pending order in Ant_latepayment
        $orders = Mage::getModel('sales/order')->getCollection()
                ->addFieldToFilter('status', array('in' => array('pending_online', 'pending_offline')))
                ->addFieldToFilter('entity_id', array('in' => $paymentordersId))
                ->addAttributeToSelect('entity_id');
        //$Ids;
        foreach ($orders as $order) {
            $Ids[] = $order->getId();
        }
        $collection = Mage::getModel('latepayment/latepayment')->getCollection()
                ->addFieldToFilter('order_id', array('nin' => $Ids));
//        ->addAttributeToSelect(array('entity_id'));
        foreach ($collection as $i) {
//            echo 'delete '.$i->getId().'<br/>';
            $i->delete();
        }
        // add new latepayment
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');

        $query = "SELECT g.entity_id, g.status, g.store_id, customer_id, base_grand_total, grand_total, increment_id, g.base_currency_code, order_currency_code, 
                shipping_name, billing_name, g.created_at, c.mw_deliverydate_date, MAX(IFNULL(v.value, 0)) AS lead_time
                FROM sales_flat_order_grid g INNER JOIN sales_flat_order_payment p ON g.entity_id = p.parent_id
                INNER JOIN sales_flat_order_item i on g.entity_id = i.order_id
                INNER JOIN mw_onestepcheckout c on c.sales_order_id = g.entity_id
                LEFT JOIN (SELECT entity_id AS product_id, value FROM catalog_product_entity_varchar 
                WHERE attribute_id = (SELECT attribute_id FROM eav_attribute WHERE attribute_code = 'lead_time')) v on v.product_id = i.product_id
            WHERE payment_validated = 0 AND g.status LIKE '%pending%' AND p.method in (" . $method . ")
            GROUP BY g.entity_id, g.status, g.store_id, customer_id, base_grand_total, grand_total, increment_id, g.base_currency_code, order_currency_code, 
		shipping_name, billing_name, g.created_at, c.mw_deliverydate_date";
        $result = $readConnection->query($query);

        while ($row = $result->fetch()) {
            $orderId = $row['entity_id'];
            $leadtime = $row['lead_time'];
            $deliverydate = $row['mw_deliverydate_date'];
//            echo $deliverydate.'<br/>';
            $day = substr($deliverydate, 8, 2);
            $mounth = substr($deliverydate, 5, 2);
            $year = substr($deliverydate, 0, 4);
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
                    $latedays = 0;
                }
                $collection = Mage::getModel('latepayment/latepayment')->getCollection()
                        ->addFieldToFilter('order_id', $orderId);
                //->addFieldToSelect(array('entity_id'));

                if (count($collection) == 0) {
                    $item = Mage::getModel('latepayment/latepayment');
                    $item->setOrder_id($orderId);
                    $item->setIncrement_id($row['increment_id']);
                    $item->setStore_id($row['store_id']);
                    $item->setCreated_at($row['created_at']);
                    $item->setBilling_name($row['billing_name']);
                    $item->setShipping_name($row['shipping_name']);
                    $item->setMw_deliverydate_date($row['mw_deliverydate_date']);
                    $item->setBase_currency_code($row['base_currency_code']);
                    $item->setBase_grand_total($row['base_grand_total']);
                    $item->setOrder_currency_code($row['order_currency_code']);
                    $item->setGrand_total($row['grand_total']);
                    $item->setStatus($row['status']);
                    $item->setLatestatus($late);
                    $item->setDaysleft($latedays);
                    $item->setSent('');
                    $item->save();
                    //Mage::getSingleton('adminhtml/session')->addSuccess('add new: '.$orderId);
                } else {
                    $item = Mage::getModel('latepayment/latepayment')->getCollection()
                            ->addFieldToFilter('order_id', $row['entity_id'])
                            ->getFirstItem();
                    $item->setLatestatus($late);
                    $item->setDaysleft($latedays);
                    $item->save();
                }
            }
        }

        /* end Late Payment */

        /* send mail notify */
        /*
        $lates = Mage::getModel('latepayment/latepayment')->getCollection();
        foreach ($lates as $late) {
            $orderId = $late->getOrder_id();
            $order = Mage::getModel('sales/order')->load($orderId);
            $email = $order->getCustomerEmail();
            //$email = 'hoang.dinh21@gmail.com';
           
            $mailSubject = 'Roses Only Singapore - Order #' . $order->increment_id . ' is late';

            $storeId = Mage::app()->getStore()->getId();
            $templateId = Mage::getStoreConfig('latepayment/latepaymentsetting/email_template', $storeId);
            echo $templateId;

            // Set sender information          
            $senderName = Mage::getStoreConfig('trans_email/ident_support/name');
            $senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');
            $sender = array('name' => $senderName, 'email' => $senderEmail);

            // Set variables that can be used in email template
            $vars = array(
                'order' => $order,
                'subject' => $mailSubject);

            // Send Transactional Email
            Mage::getModel('core/email_template')->setTemplateSubject($mailSubject)
                    ->sendTransactional($templateId, $sender, $email, 'Admin', $vars, $storeId);
        }         
         */
        /* end send mail */
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