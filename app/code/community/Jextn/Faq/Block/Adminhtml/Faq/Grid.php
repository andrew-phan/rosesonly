<?php

class Jextn_Faq_Block_Adminhtml_Faq_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  protected $_jextnUrl = 'http://www.magento-themes.jextn.com';
  public function __construct()
  {
      parent::__construct();
      $this->setId('faqGrid');
      $this->setDefaultSort('faq_id');
      $this->setDefaultDir('ASC');
  }
	
  protected function _prepareCollection()
  {
      $collection = Mage::getModel('faq/faq')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('faq_id', array(
          'header'    => Mage::helper('faq')->__('FAQ #'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'faq_id',
      ));
	
	  $this->addColumn('question', array(
		  'header'    => Mage::helper('faq')->__('Question'),
		  'align'     =>'left',
		  'index'     => 'question',
	  ));
	if (!Mage::app()->isSingleStoreMode()) {
			$this->addColumn('store_id',
					array (
							'header' => Mage::helper('faq')->__('Store view'),
							'index' => 'store_id',
							'type' => 'store',
							'store_all' => true,
							'store_view' => true,
							'sortable' => false,
							'filter_condition_callback' => array (
									$this,
									'_filterStoreCondition' ) ));
		}
		
		

	  $this->addColumn('status', array(
          'header'    => Mage::helper('faq')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
          ),
      ));
	$this->addColumn('created_time', array(
            'header'    => Mage::helper('cms')->__('Date Created'),
            'index'     => 'created_time',
			'width'     => '150px',
            'type'      => 'datetime',
        ));	

        $this->addColumn('update_time', array(
            'header'    => Mage::helper('cms')->__('Last Modified'),
            'index'     => 'update_time',
			'width'     => '150px',
            'type'      => 'datetime',
        ));		
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('faq')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('faq')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
  
      return parent::_prepareColumns();
  }

   
	protected function _afterToHtml($html)
	{
		return parent::_afterToHtml($html). $this->_appendHtml();
	}
	private function _appendHtml()
    {
    	$html=
		'
		<style type="text/css">
		<!--
		#jextn-href { text-align:right; font-size:9px; }
		#jextn-href a{ text-decoration:none; color:#2F2F2F; }
    	#jextn-href a:hover { text-decoration:none;  }
		-->
		</style>
		<div id="jextn-href">Community version of FAQ - Jextn <a href="'.$this->_jextnUrl.'" title="Magento Themes" target="_blank">Magento Themes</a></div>
		';
    return $html;
    }
	protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }
	
	public function getRowUrl($row)
	{
	  return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}
  
	protected function _filterStoreCondition($collection, $column)
	{
		if (!$value = $column->getFilter()->getValue()) {
			return;
		}

		$this->getCollection()->addStoreFilter($value);
	}

}