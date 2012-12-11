<?php

class Liip_Xhprof_Model_Collection extends Varien_Data_Collection
{
    protected $calls;
    protected $parent;

    public function setCalls($calls)
    {
        $this->calls = $calls;
    }

    public function setCurrent($current = null)
    {
        $this->current = $current;
    }

    public function load($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }

        // Collect
        $calls = array();

        foreach ($this->calls as $call => $time) {
            // || empty($this->calls['parent'])
            if ($call == 'main()') {
                $parent = null;
                $child = $call;
            } else {
                list($parent, $child) = explode('==>', $call);
            }
            if ($this->current) {
                if ($child == $this->current) {
                    if (!isset($calls[$parent])) {
                        $calls[$parent] = array(
                            'name' => "parent -> ".$parent,
                            'parent' => $parent,
                            'count' => 0,
                            'time' => 0,
                            'excl_time' => 0,
                        );
                        $calls[$parent]['excl_time'] -= $time['wt'];
                    }

                } elseif ($parent == $this->current) {
                    if (!isset($calls[$child])) {
                        $calls[$child] = array(
                            'name' => "child ---> " . $child,
                            'parent' => $parent,
                            'count' => 0,
                            'time' => 0,
                            'excl_time' => 0,
                        );
                        $calls[$child]['count'] += $time['ct'];
                        $calls[$child]['time'] += $time['wt'];
                        $calls[$child]['excl_time'] += $time['wt'];
                    }
                }
            } else {
                if (!isset($calls[$child])) {
                    $calls[$child] = array(
                        'name' => $child,
                        'parent' => $parent,
                        'count' => 0,
                        'time' => 0,
                        'excl_time' => 0,
                    );
                }

                $calls[$child]['count'] += $time['ct'];
                $calls[$child]['time'] += $time['wt'];
                $calls[$child]['excl_time'] += $time['wt'];


                if ($parent) {
                    if (!isset($calls[$parent])) {
                        $calls[$parent] = array(
                            'name' => $parent,
                            'parent' => $parent,
                            'count' => 0,
                            'time' => 0,
                            'excl_time' => 0,
                        );
                    }
                    $calls[$parent]['excl_time'] -= $time['wt'];
                }
            }

        }

        // Sort
        $sort = '';
        $dir = '';
        foreach ($this->_orders as $field => $direction) {
            $sort = $field;
            $dir = $direction;
        }

        usort($calls, function ($a, $b) use ($sort, $dir) {
            if (!isset($a[$sort]) || !isset($b[$sort])) {
                return 0;
            }
            if ($a[$sort] == $b[$sort]) {
                return 0;
            }
            return ($a[$sort] > $b[$sort] xor $dir == Varien_Data_Collection::SORT_ORDER_DESC) ? -1 : 1;
        });

        // Limit
        $calls = array_slice($calls, ($this->getCurPage() - 1) * $this->_pageSize, $this->_pageSize);

        foreach ($calls as $call) {
            $this->addItem(new Varien_Object($call));
        }

        $this->_setIsLoaded();

        return $this;
    }

    public function getSize()
    {
        return count($this->calls);
    }
}
