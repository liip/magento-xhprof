<?php

class Liip_Xhprof_Model_Resource_Run extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('liip_xhprof/run', 'id');
    }
}
