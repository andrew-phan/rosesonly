<?php

class Ant_Deliverymanagement_Block_Adminhtml_Updatestatus_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId("updatestatusGrid");
        $this->setDefaultSort("assign_id");
        $this->setDefaultDir("DESC");
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $user = Mage::getSingleton('admin/session')->getUser();

        $userId = $user->getUserId();

        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $query = "select role_name from admin_role
            where role_id = (select parent_id from admin_role where user_id = '" . $userId . "')";
        $rolename = $readConnection->fetchOne($query);

        //If role_name is "Driver", the user only can see delivery assigned for him.
        if (strcasecmp($rolename, "Driver") == 0) {
            $collection = Mage::getModel("deliverymanagement/deliverymanagement")
                    ->getCollection()
                    ->addFieldToFilter('updatestatus', array('eq' => 1))
                    ->addFieldToFilter('user_id', array('eq' => $userId))
                    ->load();
        } else {
            $collection = Mage::getModel("deliverymanagement/deliverymanagement")->getCollection()
                    ->addFieldToFilter('updatestatus', array('eq' => 1));
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $deliveryoption['assigned'] = 'Assigned';
        $deliveryoption['intransit'] = 'In Transit';
        $deliveryoption['completed'] = 'Completed';
        
        $this->addColumn("assign_id", array(
            "header" => Mage::helper("deliverymanagement")->__("ID"),
            "align" => "right",
            "width" => "50px",
            "index" => "assign_id",
        ));
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
        
        $this->addColumn('mw_customercomment_info', array(
            'header'    => Mage::helper('sales')->__('Customer\'s comment'),
            'index'     => 'mw_customercomment_info',
            'type'      => 'text',
            'align'     => 'center',
        ));
        /* $this->addColumn("update_at", array(
          "header" => Mage::helper("deliverymanagement")->__("Update"),
          "align" =>"left",
          "index" => "update_at",
          )); */
        $this->addColumn("status", array(
            "header" => Mage::helper("deliverymanagement")->__("Status"),
            "align" => "left",
            "type" => "options",
            "index" => "status",
            "options" => $deliveryoption,
        ));

        return parent::_prepareColumns();
    }
    
    protected function _prepareMassaction() {
        $this->setMassactionIdField('assign_id');
        $this->getMassactionBlock()->setFormFieldName('assign_id');
        $this->getMassactionBlock()->addItem('intransit', array(
            'label' => 'In transit',
            'url' => $this->getUrl('*/*/update/status/intransit'),
            'confirm' => 'Are you sure?'
        ));
        $this->getMassactionBlock()->addItem('completed', array(
            'label' => 'Completed',
            'url' => $this->getUrl('*/*/update/status/completed'),
            'confirm' => 'Are you sure?'
        ));
        return $this;
    }

    public function getRowUrl($row) {
        //return $this->getUrl("*/*/edit", array("id" => $row->getId()));
        return Mage::getModel('adminhtml/url')->getUrl('adminhtml/sales_shipment/view', array("shipment_id" => $row->getShipment_id()));
    }

}