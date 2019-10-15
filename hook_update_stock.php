<?php
require 'app/Mage.php';

if (!Mage::isInstalled()) {
    echo "Application is not installed yet, please complete install wizard first.";
    exit;
}

// Only for urls
// Don't remove this
$_SERVER['SCRIPT_NAME'] = str_replace(basename(__FILE__), 'index.php', $_SERVER['SCRIPT_NAME']);
$_SERVER['SCRIPT_FILENAME'] = str_replace(basename(__FILE__), 'index.php', $_SERVER['SCRIPT_FILENAME']);

Mage::app('admin')->setUseSessionInUrl(false);

umask(0);

try {
    Mage::getConfig()->init();
    Mage::getConfig()->loadModules();
} catch (Exception $e) {
    Mage::printException($e);
}


$debug = '<h1>Update stocks for orders</h1>';

//collect orders with stocks_updated = 0 and status not finished (complete or canceled)
$app = Mage::app();
$collection = Mage::getModel('sales/order')
    ->getCollection()
    ->addFieldToFilter('stocks_updated', '0')
    ->addAttributeToSelect('*')
    ->addAttributeToFilter('state', array('nin' => array('complete', 'canceled')));

$maxOrder = (int) Mage::getStoreConfig('advancedstock/cron/order_update_stocks_max');
$count = 0;
foreach ($collection as $order) {
    $debug .= '<p><b>Processing order #' . $order->getId() . ' at (' . date('Y-m-d H:i:s') . ')</b>';

    try {
        //parse each product
        foreach ($order->getAllItems() as $item) {
            $productId = $item->getproduct_id();

            //get preparation warehouse
            $preparationWarehouseId = Mage::helper('AdvancedStock/Router')->getWarehouseForOrderItem($item, $order);
            if (!$preparationWarehouseId)
                $preparationWarehouseId = 1;

            //Affect order item to warehouse
            Mage::helper('AdvancedStock/Router')->affectWarehouseToOrderItem(array('order_item_id' => $item->getId(), 'warehouse_id' => $preparationWarehouseId));
        }

        //update stocks_updated
        if ($order->getPayment()) { {
            $debug .= '<br>Set stocks updated = 1 for order #' . $order->getId();
//            $this->setStocksAsUpdated($order);

            $prefix = Mage::getConfig()->getTablePrefix();
            $sql = 'update '.$prefix.'sales_flat_order set stocks_updated = 1 where entity_id = '.$order->getId();
            Mage::getResourceModel('catalog/product')->getReadConnection()->query($sql);

            Mage::dispatchEvent('advancedstock_order_considered_by_cron', array('order_id' => $order->getId()));
        }
        }
        else
            $debug .= '<br>--->Unable to retrieve payment for order #' . $order->getId();

        //execut X orders at once
        if ($count > $maxOrder) {
            $debug .= '<br>Exit after ' . $maxOrder . ' loops';
            break;
        }
        $count++;
    } catch (Exception $ex) {
        Mage::logException($ex);
        $debug .= '<p>Error updating stocks for order #' . $order->getId() . ' : ' . $ex->getMessage() . '</p>';
    }
}

//print debug informaiton
if (Mage::getStoreConfig('advancedstock/cron/debug'))
    echo $debug;
Mage::log("Update stock okay");
echo "Updated okay";
?>