<?php
/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersPro
 * @copyright  Copyright (c) 2012 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * Orders Pro extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersPro
 * @author     MageWorx Dev Team
 */
if (Mage::getConfig()->getModuleConfig('LucidPath_SalesRep')->is('active', true)) {
    class MageWorx_Adminhtml_Block_Orderspro_Sales_Order_Grid_Abstract extends Mage_Adminhtml_Block_Sales_Order_Grid {
        public function setCollection($collection) {
            $collection->getSelect()->joinLeft(array('salesrep' => $collection->getTable('salesrep/salesrep')), 'salesrep.order_id=entity_id');
            return parent::setCollection($collection);
        }
        protected function _prepareSalesRepColumns() {
            if (Mage::getStoreConfig('salesrep/order_grid/commission_earner')) {
                $this->addColumn('admin_name', array(
                    'header' => Mage::helper('sales')->__('Comm. Earner'),
                    'index' => 'admin_name',
                    'align' => 'center',
                    'width' => '10px',
                    'renderer' => 'LucidPath_SalesRep_Block_Adminhtml_Order_Grid_Renderer_Earner',
                ));
            }

            if (Mage::getStoreConfig('salesrep/order_grid/commission_amount')) {
                $this->addColumn('commission_earned', array(
                    'header' => Mage::helper('sales')->__('Comm. Amount'),
                    'index' => 'commission_earned',
                    'align' => 'center',
                    'width' => '10px',
                    'renderer' => 'LucidPath_SalesRep_Block_Adminhtml_Order_Grid_Renderer_Amount',
                ));
            }

            if (Mage::getStoreConfig('salesrep/order_grid/commission_payment_status')) {
                $this->addColumn('commission_status', array(
                    'header' => Mage::helper('sales')->__('Comm. Status'),
                    'index' => 'commission_status',
                    'align' => 'center',
                    'width' => '10px',
                    'renderer' => 'LucidPath_SalesRep_Block_Adminhtml_Order_Grid_Renderer_PaymentStatus',
                ));
            }
        }        
    }
} else if (Mage::getConfig()->getModuleConfig('Mage_CanPostExport')->is('active', true)) {
    class MageWorx_Adminhtml_Block_Orderspro_Sales_Order_Grid_Abstract extends Mage_CanPostExport_Block_Sales_Order_Grid {}
} else if (Mage::getConfig()->getModuleConfig('Magemaven_OrderComment')->is('active', true)) {
    class MageWorx_Adminhtml_Block_Orderspro_Sales_Order_Grid_Abstract extends Magemaven_OrderComment_Block_Adminhtml_Sales_Order_Grid {}
} else if ((string)Mage::getConfig()->getModuleConfig('Extended_Ccsave')->active=='true'){
    class MageWorx_Adminhtml_Block_Orderspro_Sales_Order_Grid_Abstract extends Extended_Ccsave_Block_Adminhtml_Sales_Order_Grid {}
} else if ((string)Mage::getConfig()->getModuleConfig('Directshop_FraudDetection')->active=='true'){
    class MageWorx_Adminhtml_Block_Orderspro_Sales_Order_Grid_Abstract extends Directshop_FraudDetection_Block_Adminhtml_Sales_Order_Grid {
   	public function setCollection($collection) {
            $collection->getSelect()->joinLeft(array('frauddetection_data' => $collection->getTable('frauddetection/result')), 'frauddetection_data.order_id=main_table.entity_id', 'fraud_score');
            return parent::setCollection($collection);
        }
        protected function _prepareFraudDetectionColumns() {
            $this->addColumn('fraud_score', array(
                'header'=> Mage::helper('sales')->__('Fraud<br/>Score'),
                'width' => '15px',
                'type'  => 'number',
                'index' => 'fraud_score',
                'filter_condition_callback' => array($this, '_filterFraudScore'),
                'align' => 'center',
                'filter' => 'adminhtml/widget_grid_column_filter_range',
                'renderer'  => 'Directshop_FraudDetection_Block_Adminhtml_Widget_Grid_Column_Renderer_Fraudscore',
            ), 'status');
        }        
    }
} else if ((string)Mage::getConfig()->getModuleConfig('Amasty_Orderattach')->active=='true'){
    class MageWorx_Adminhtml_Block_Orderspro_Sales_Order_Grid_Abstract extends Mage_Adminhtml_Block_Sales_Order_Grid {
        protected function _prepareAmastyOrderattachColumns() {
            $attachments = Mage::getModel('amorderattach/field')->getCollection();
            $attachments->addFieldToFilter('show_on_grid', 1);
            $attachments->load();
            if ($attachments->getSize()) {
                foreach ($attachments as $attachment) {
                    switch ($attachment->getType()) {
                        case 'date':
                            $this->addColumn($attachment->getFieldname(), array(
                                'header' => $this->__($attachment->getLabel()),
                                'type' => 'date',
                                'align' => 'center',
                                'index' => $attachment->getFieldname(),
                                'gmtoffset' => false,
                            ));
                            break;
                        case 'text':
                        case 'string':
                            $this->addColumn($attachment->getFieldname(), array(
                                'header' => $this->__($attachment->getLabel()),
                                'index' => $attachment->getFieldname(),
                                'filter' => 'adminhtml/widget_grid_column_filter_text',
                                'sortable' => true,
                            ));
                            break;
                        case 'select':
                            $selectOptions = array();
                            $options = explode(',', $attachment->getOptions());
                            $options = array_map('trim', $options);
                            if ($options) {
                                foreach ($options as $option) {
                                    $selectOptions[$option] = $option;
                                }
                            }
                            $this->addColumn($attachment->getFieldname(), array(
                                'header' => $this->__($attachment->getLabel()),
                                'index' => $attachment->getFieldname(),
                                'type' => 'options',
                                'options' => $selectOptions,
                            ));
                            break;
                    }
                }
            }            
        }
    }
} else if ((string)Mage::getConfig()->getModuleConfig('Amasty_Flags')->active=='true'){
    class MageWorx_Adminhtml_Block_Orderspro_Sales_Order_Grid_Abstract extends Amasty_Flags_Block_Rewrite_Adminhtml_Order_Grid {
        protected function _prepareAmastyFlagsColumns() {
            $flagCollection = Mage::getModel('amflags/flag')->getCollection();
            $flagFilterOptions = array();
            if ($flagCollection->getSize() > 0) {
                foreach ($flagCollection as $flag) {
                    $flagFilterOptions[$flag->getPriority()] = $flag->getAlias();
                }
            }
            $flagColumn = $this->getLayout()->createBlock('adminhtml/widget_grid_column')
                    ->setData(array(
                        'header' => Mage::helper('amflags')->__('Flag'),
                        'index' => 'priority',
                        'width' => '80px',
                        'align' => 'center',
                        'renderer' => 'amflags/adminhtml_renderer_flag',
                        'type' => 'options',
                        'options' => $flagFilterOptions,
                    ))
                    ->setGrid($this)
                    ->setId('flag_id');
            // adding flag column to the beginning of the columns array
            $flagColumnArray = array('flag_id' => $flagColumn);
            $this->_columns = $flagColumnArray + $this->_columns;
        }
    }
} else if ((string)Mage::getConfig()->getModuleConfig('Amasty_Email')->active=='true'){
    class MageWorx_Adminhtml_Block_Orderspro_Sales_Order_Grid_Abstract extends Amasty_Email_Block_Adminhtml_Sales_Order_Grid {}    
} else if ((string)Mage::getConfig()->getModuleConfig('AdjustWare_Deliverydate')->active=='true'){
    class MageWorx_Adminhtml_Block_Orderspro_Sales_Order_Grid_Abstract extends AdjustWare_Deliverydate_Block_Rewrite_AdminhtmlSalesOrderGrid {}
} else {
    // Phuoc's code -----------------------------------
//    class MageWorx_Adminhtml_Block_Orderspro_Sales_Order_Grid_Abstract extends Mage_Adminhtml_Block_Sales_Order_Grid {}    
    class MageWorx_Adminhtml_Block_Orderspro_Sales_Order_Grid_Abstract extends MW_Onestepcheckout_Block_Adminhtml_Onestepcheckout_Sales_Order_Grid {}    
    // end code
}