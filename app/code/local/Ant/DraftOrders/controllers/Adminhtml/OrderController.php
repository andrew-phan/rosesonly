<?php
class Ant_DraftOrders_Adminhtml_OrderController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_title($this->__('Sales'))->_title($this->__('Orders Inchoo'));
        $this->loadLayout();
        $this->_setActiveMenu('sales/sales');
        $this->_addContent($this->getLayout()->createBlock('ant_draftorders/adminhtml_sales_order'));
        $this->renderLayout();
    }
    
    // Check order view availability
    protected function _canViewOrder($order)
    {                
        if (Mage::helper('orderspro')->isHideDeletedOrdersForCustomers()) {
            $orderItemGroup = Mage::getModel('orderspro/order_item_group')->load($order->getId(), 'order_id');
            if ($orderItemGroup->getOrderGroupId()==2) return false;
        }        
        
        return parent::_canViewOrder($order);
    }   
    
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('ant_draftorders/adminhtml_sales_order_grid')->toHtml()
        );
    }
    public function exportInchooCsvAction()
    {
        $fileName = 'orders_inchoo.csv';
        $grid = $this->getLayout()->createBlock('ant_draftorders/adminhtml_sales_order_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }
    public function exportInchooExcelAction()
    {
        $fileName = 'orders_inchoo.xml';
        $grid = $this->getLayout()->createBlock('ant_draftorders/adminhtml_sales_order_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
}