<?php

/**
 * Yumba Soft
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Yumba
 * @package    Yumba_OrderCancel
 * @copyright  Copyright (c) 2011 Yumba Soft (yumba.soft@gmail.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Orders history block
 *
 * @author Yumba Soft <yumba.soft@gmail.com>
 * @see Mage_Sales_Block_Order_History
 */
class Yumba_OrderCancel_Block_Order_History extends Mage_Sales_Block_Order_History
{
    public function __construct()
    {
        parent::__construct();
        
        // Replace default template
        $this->setTemplate('ordercancel/sales/order/history.phtml');
    }

}
