<?php

class Liip_Xhprof_Model_Run extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('liip_xhprof/run');
    }
}
