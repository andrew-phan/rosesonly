<?php

class Ant_Deliverymanagement_Block_Adminhtml_Arrangedriver_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId("arrangedriverGrid");
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $shipAssignedcolect = Mage::getModel("deliverymanagement/deliverymanagement")->getCollection();
        foreach ($shipAssignedcolect as $shipAssign){
            $shipIds[] = $shipAssign->getShipment_id();
        }
        
        $collection = Mage::getResourceModel('sales/order_shipment_grid_collection')
                ->addFieldToFilter("entity_id", array("nin" => $shipIds));
        $collection->getSelect()->joinleft(array('one_step'=> 'mw_onestepcheckout'),'one_step.sales_order_id=main_table.order_id',array('mw_deliverydate_date','mw_customercomment_info','mw_deliverydate_time'));
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn("entity_id", array(
            "header" => Mage::helper("deliverymanagement")->__("ID"),
            "align" => "right",
            "width" => "50px",
            "index" => "entity_id",
        ));

        $this->addColumn('increment_id', array(
            'header'    => Mage::helper('sales')->__('Shipment #'),
            'index'     => 'increment_id',
            'type'      => 'text',
        ));
        
        $this->addColumn('created_at', array(
            'header'    => Mage::helper('sales')->__('Shipment Created'),
            'index'     => 'created_at',
            'type'      => 'datetime',
        ));
        
        $this->addColumn('order_increment_id', array(
            'header'    => Mage::helper('sales')->__('Order #'),
            'index'     => 'order_increment_id',
            'type'      => 'text',
        ));
        
        $this->addColumn('order_created_at', array(
            'header'    => Mage::helper('sales')->__('Order Date'),
            'index'     => 'order_created_at',
            'type'      => 'datetime',
        ));
        
        $this->addColumn('mw_deliverydate_date', array(
            'header'    => Mage::helper('sales')->__('Delivery date'),
            'index'     => 'mw_deliverydate_date',
            'type'      => 'date',
        ));
        
        $this->addColumn('mw_deliverydate_time', array(
            'header'    => Mage::helper('sales')->__('Delivery time'),
            'index'     => 'mw_deliverydate_time',
            'type'      => 'text',
            'align'     => 'center',
        ));
        
        $this->addColumn('shipping_name', array(
            'header' => Mage::helper('sales')->__('Ship to Name'),
            'index' => 'shipping_name',
        ));

        $this->addColumn('total_qty', array(
            'header' => Mage::helper('sales')->__('Total Qty'),
            'index' => 'total_qty',
            'type'  => 'number',
        ));
        

        return parent::_prepareColumns();
    }
    
    protected function _prepareMassaction() {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('entity_id');
        
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $query = "select firstname, lastname, username, admin_user.user_id
            from (select user_id from admin_role
            where parent_id = (select role_id from admin_role where role_name like 'driver')) t inner join admin_user
            on t.user_id = admin_user.user_id";
        $result = $readConnection->query($query);
        
        while ($row = $result->fetch()){
            $this->getMassactionBlock()->addItem('Assign'.$row['user_id'], array(
            'label' => 'Assign '.$row['firstname'].' '.$row['last_name'],
            'url' => $this->getUrl('*/*/assign/driver/'.$row['user_id']),
            //'url' => $this->getUrl('*/*/assign/'),
            'confirm' => 'Are you sure?'
        ));
        }
        return $this;
    }

    public function getRowUrl($row) {
        //return $this->getUrl("*/*/edit", array("id" => $row->getId()));
        return Mage::getModel('adminhtml/url')->getUrl('adminhtml/sales_shipment/view', array("shipment_id" => $row->getId()));
    }

}