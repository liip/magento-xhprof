<?php

class Liip_Xhprof_Model_Observer
{
    /**
     * Event handler for `controller_front_init_before`
     */
    public function start($observer)
    {
        if (Mage::helper('liip_xhprof')->isEnabled()) {

            xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);
        }
    }

    /**
     * Event handler for `controller_front_send_response_after`
     */
    public function stop($observer)
    {
        if (Mage::helper('liip_xhprof')->isEnabled()) {

            $data = xhprof_disable();

            $run = Mage::getModel('liip_xhprof/run');
            $run->addData(array(
                'data' => json_encode($data),
                'path_info' => Mage::app()->getRequest()->getPathInfo(),
                'pmu' => isset($data['main()']['pmu']) ? $data['main()']['pmu'] : 0,
                'wt' => isset($data['main()']['wt'])  ? $data['main()']['wt']  : 0,
                'cpu' => isset($data['main()']['cpu']) ? $data['main()']['cpu'] : 0,
                'created_at' => date('c'),        
            ));

            $run->save();
        }
    }
}
