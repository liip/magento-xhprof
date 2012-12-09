<?php

class Liip_Xhprof_Block_Report_Runs extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'liip_xhprof';
        $this->_controller = 'report_runs';
        $this->_headerText = $this->__('Runs');

        parent::__construct();
        $this->_removeButton('add');
    }
}
