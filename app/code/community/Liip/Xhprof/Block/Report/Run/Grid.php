<?php

class Liip_Xhprof_Block_Report_Run_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
       parent::__construct();

       // Set some defaults for our grid
       $this->setDefaultSort('excl_time');
       $this->setId('liip_xhprof_report_run');
       $this->setDefaultDir('asc');
       $this->setFilterVisibility(false);
       $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        // Get and set our collection for the grid
        if (!$this->getCollection()) {
            $collection = Mage::getModel('liip_xhprof/collection');

            $id = Mage::app()->getRequest()->getParam('id');
            $parent = Mage::app()->getRequest()->getParam('parent');

            $runs = Mage::getModel('liip_xhprof/run')->getCollection();
            $runs->addFieldToFilter('id', $id);

            $row = $runs->getFirstItem();

            $data = json_decode($row->getData('data'), true);

            $collection->setCalls($data);
            $collection->setCurrent($parent);

            $this->setCollection($collection);

            return parent::_prepareCollection();
        }
    }

    protected function _prepareColumns()
    {

       $this->addColumn('name',
           array(
              'header'=> $this->__('Name'),
              'index' => 'name',
              'sortable' => false,
           )
       );

       $this->addColumn('excl_time',
           array(
              'header'=> $this->__('Excl. Wall Time'),
              'index' => 'excl_time',
              'type' => 'number',
              'width' => 200,
           )
       );

       $this->addColumn('time',
           array(
              'header'=> $this->__('Wall Time'),
              'index' => 'time',
              'type' => 'number',
              'width' => 200,
           )
       );

       $this->addColumn('count',
           array(
              'header'=> $this->__('Count'),
              'index' => 'count',
              'type' => 'number',
              'width' => 200,
           )
       );

       return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        $id = Mage::app()->getRequest()->getParam('id');
        return $this->getUrl('*/*/run', array('id' => $id, 'parent' => $row->getName()));
    }

    public function getGridUrl()
     {
        return $this->getUrl('*/*/runGrid', array('_current'=>true));
     }
}
