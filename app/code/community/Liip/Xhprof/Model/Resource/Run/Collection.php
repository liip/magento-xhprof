<?php

class Liip_Xhprof_Model_Resource_Run_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('liip_xhprof/run');
    }
}
