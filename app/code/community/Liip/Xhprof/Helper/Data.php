<?php

class Liip_Xhprof_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function isEnabled()
    {
        return extension_loaded('xhprof');
    }
}
