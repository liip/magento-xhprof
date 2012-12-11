<?php

class Liip_Xhprof_Block_Report_Run extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $parent = Mage::app()->getRequest()->getParam('parent');
        $header = 'Run';
        $this->_blockGroup = 'liip_xhprof';
        $this->_controller = 'report_run';
        if ($parent) {
            $header = $parent;
        }
        $this->_headerText = $this->__($header);

        parent::__construct();

        $this->_removeButton('add');
        $this->_addButton('back', array(
            'label'     => Mage::helper('adminhtml')->__('Back'),
            'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/index') . '\')',
            'class'     => 'back',
        ), -1);
    }
}
