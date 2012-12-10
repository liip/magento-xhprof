<?php

class Liip_Xhprof_Model_Observer
{
    protected $profiling = false;

    /**
     * Event handler for `controller_front_init_before`
     */
    public function start($observer)
    {
        if (!Mage::helper('liip_xhprof')->isEnabled()) {
            return;
        }

        $sampleSize = Mage::app()->getStore()->getConfig('dev/liip_xhprof/sample_size');
        $includes = explode("\n", Mage::app()->getStore()->getConfig('dev/liip_xhprof/include_paths'));
        $excludes = explode("\n", Mage::app()->getStore()->getConfig('dev/liip_xhprof/exclude_paths'));

        $isSample = (mt_rand(1, $sampleSize) == 1);

        $url = Mage::app()->getRequest()->getPathInfo();

        $isIncluded = false;
        foreach ($includes as $include) {
            if (strpos($url, $include) === 0) {
                $isIncluded = true;
            }
        }

        $isExcluded = false;
        foreach ($excludes as $exclude) {
            if (strpos($url, $exclude) === 0) {
                $isExcluded = true;
            }
        }

        if ($isSample && $isIncluded && !$isExcluded) {

            xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);

            $this->profiling = true;
        }
    }

    /**
     * Event handler for `controller_front_send_response_after`
     */
    public function stop($observer)
    {
        if ($this->profiling) {

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
