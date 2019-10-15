<?php

class Egrove_Amex_Block_Adminhtml_Amexdata_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('amexGrid');
      $this->setDefaultSort('id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('amex/amexdata')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('id', array(
          'header'    => Mage::helper('amex')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'id',
      ));

      $this->addColumn('amount', array(
          'header'    => Mage::helper('amex')->__('Amount'),
          'align'     =>'left',
          'index'     => 'amount',
      ));
      $this->addColumn('order_id', array(
          'header'    => Mage::helper('amex')->__('Order Id'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'order_id',
      ));

      $this->addColumn('authorized_id', array(
          'header'    => Mage::helper('amex')->__('Authorized Id'),
          'align'     =>'left',
          'index'     => 'authorized_id',
      ));
      $this->addColumn('message', array(
          'header'    => Mage::helper('amex')->__('Message'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'message',
      ));

      $this->addColumn('transation_no', array(
          'header'    => Mage::helper('amex')->__('Transation No'),
          'align'     =>'left',
          'index'     => 'transation_no',
      ));
      $this->addColumn('capture_message', array(
          'header'    => Mage::helper('amex')->__('Capture Message'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'capture_message',
      ));

      $this->addColumn('capture_tno', array(
          'header'    => Mage::helper('amex')->__('Capture Transation Number'),
          'align'     =>'left',
          'index'     => 'capture_tno',
	));
      $this->addColumn('capture_rno', array(
          'header'    => Mage::helper('amex')->__('Capture Receipt Number'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'capture_rno',
      ));

      $this->addColumn('capture_amount', array(
          'header'    => Mage::helper('amex')->__('Capture Amount'),
          'align'     =>'left',
          'index'     => 'capture_amount',
	));
      
      	
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('amex');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('amex')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('amex')->__('Are you sure?')
        ));

        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}