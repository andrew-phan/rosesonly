<?php

class Ant_Deliverymanagement_Block_Adminhtml_Deliverymanagement_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId("deliverymanagementGrid");
        $this->setDefaultSort("increment_id");
        $this->setDefaultDir("DESC");
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel("deliverymanagement/deliverymanagement")->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $deliveryoption['assigned'] = 'Assigned';
        $deliveryoption['intransit'] = 'In Transit';
        $deliveryoption['completed'] = 'Completed';
        
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $query = "select firstname, lastname, username, admin_user.user_id
            from (select user_id from admin_role
            where parent_id = (select role_id from admin_role where role_name like 'driver')) t inner join admin_user
            on t.user_id = admin_user.user_id";
        $result = $readConnection->query($query);
        
        while ($row = $result->fetch()){
            $usersoption[$row['user_id']] = $row['firstname'].' '.$row['last_name'];
        }
        
        $updateoption[1] = 'Enable';
        $updateoption[0] = 'Disable';
        
//        $this->addColumn("assign_id", array(
//            "header" => Mage::helper("deliverymanagement")->__("ID"),
//            "align" => "right",
//            "width" => "50px",
//            "index" => "assign_id",
//        ));
        
        $this->addColumn("increment_id", array(
            "header" => Mage::helper("deliverymanagement")->__("Shipment #"),
            "align" => "left",
            "index" => "increment_id",
        ));
        $this->addColumn("created_at", array(
            "header" => Mage::helper("deliverymanagement")->__("Date Shipped"),
            "align" => "left",
            'type' => 'datetime',
            "index" => "created_at",
        ));
        $this->addColumn("assigneddate", array(
            "header" => Mage::helper("deliverymanagement")->__("Assigned date"),
            "align" => "left",
            'type' => 'datetime',
            "index" => "assigneddate",
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
        $this->addColumn("status", array(
            "header" => Mage::helper("deliverymanagement")->__("Status"),
            "align" => "center",
            "type" => "options",
            "options" => $deliveryoption,
            "index" => "status",
        ));
        
        
        $this->addColumn("user", array(
            "header" => Mage::helper("deliverymanagement")->__("User"),
            "align" => "left",
            "type" => "options",
            "index" => "user_id",
            "options" => $usersoption,
        ));

        $this->addColumn("updatestatus", array(
            "header" => Mage::helper("deliverymanagement")->__("Update Status"),
            "align" => "center",
            "type" => "options",
            "index" => "updatestatus",
            "options" => $updateoption,
        ));
        
        return parent::_prepareColumns();
    }

    protected function _prepareMassaction() {
        $this->setMassactionIdField('assign_id');
        $this->getMassactionBlock()->setFormFieldName('assign_id');
        $this->getMassactionBlock()->addItem('disupdate', array(
            'label' => 'Disable Status Update',
            'url' => $this->getUrl('*/*/disable/'),
            'confirm' => 'Are you sure?'
        ));
        
        return $this;
    }
    
    public function getRowUrl($row) {
        //return $this->getUrl("*/*/edit", array("id" => $row->getId()));
        return Mage::getModel('adminhtml/url')->getUrl('adminhtml/sales_shipment/view', array("shipment_id" => $row->getShipment_id()));
    }

}