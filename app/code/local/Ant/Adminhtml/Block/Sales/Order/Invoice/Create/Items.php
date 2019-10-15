<?php

class Ant_Adminhtml_Block_Sales_Order_Invoice_Create_Items extends Mage_Adminhtml_Block_Sales_Order_Invoice_Create_Items
{
	public function canEditQty()
    {
        return false;
    }
}