<?php

class Liip_Xhprof_Block_Report_Runs_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
       parent::__construct();

        $this->setTemplate('liip_xhprof/report/runs/grid.phtml');

       // Set some defaults for our grid
       $this->setDefaultSort('created_at');
       $this->setId('liip_xhprof_report_runs');
       $this->setDefaultDir('desc');
       $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
       // Get and set our collection for the grid
       $collection = Mage::getModel('liip_xhprof/run')->getCollection();
       $this->setCollection($collection);

       return parent::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {
       
       $this->addColumn('id',
           array(
              'header'=> $this->__('id'),
              'index' => 'id',
              'sortable' => false,
           )
       );
       
       $this->addColumn('path_info',
           array(
              'header'=> $this->__('Path'),
              'index' => 'path_info',
              'type' => 'text', 
              'truncate' => 100,
           )
       );
       
       $this->addColumn('created_at',
           array(
              'header'=> $this->__('Created at'),
              'index' => 'created_at',
              'type' => 'datetime',
           )
       );
       
       $this->addColumn('wt',
           array(
              'header'=> $this->__('Wall time'),
              'index' => 'wt',
              'type' => 'number',
           )
       );
       
       return parent::_prepareColumns();
    }
    
    public function getRowUrl($row)
    {
       // This is where our row data will link to
       return $this->getUrl('*/*/run', array('id' => $row->getId()));
    }

    public function getGridUrl()
     {
       return $this->getUrl('*/*/grid', array('_current'=>true));
     }
    /**
     * Api URL
     */
    const API_URL = 'http://chart.apis.google.com/chart';

    /**
     * All series
     *
     * @var array
     */
    protected $_allSeries = array();
    
    /**
     * Axis labels
     *
     * @var array
     */
    protected $_axisLabels = array();

    /**
     * Axis maps
     *
     * @var array
     */
    protected $_axisMaps = array();

    /**
     * Extended encoding chars
     *
     * @var string
     */
    protected $_extendedEncoding = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-.';

    /**
     * Chart width
     *
     * @var string
     */
    protected $_width = '1000';

    /**
     * Chart height
     *
     * @var string
     */
    protected $_height = '100';

    /**
     * Get all series
     *
     * @return array
     */
    public function getAllSeries()
    {
        return $this->_allSeries;
    }
    
    /**
     * Get chart url
     *
     * @param bool $directUrl
     * @return string
     */
    public function getChartUrl($directUrl = true)
    {
        $params = array(
            'cht'  => 'lxy',
            'chf'  => 'bg,s,f4f4f4|c,lg,90,ffffff,0.1,ededed,0',
            'chm'  => 'B,f4d4b2,0,0,0',
            'chco' => 'db4814',
            'chxt' => 'xy',
        );

        $this->setDataRows('cpu');
        $this->_allSeries = array();
        
        $items = $this->getCollection()->getItems();
        $options = array();
        foreach ($items as $item){
            $this->_allSeries['cpu'][strtotime($item->getCreatedAt())] = $item->getCpu();
        }

        $this->_axisLabels = array(
            'x' => array('range'),
            'y' => array('quantity')
        );

    
        $timezoneLocal = Mage::app()->getStore()->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE);

        list ($dateStart, $dateEnd) = Mage::getResourceModel('reports/order_collection')
            ->getDateRange('1m', '', '', true);

        $dateStart->setTimezone($timezoneLocal);
        $dateEnd->setTimezone($timezoneLocal);

        $dates = array();
        $datas = array();

        while($dateStart->compare($dateEnd) < 0){
            $d = $dateStart->toString('yyyy-MM-dd');
            $dateStart->addDay(1);
            $dates[] = $d;
        }
        
        //Google encoding values
        // extended encoding
        $params['chd'] = "e:";
        $dataDelimiter = "";
        $dataSetdelimiter = ",";
        $dataMissing = "__";

        // process each string in the array, and find the max length
        foreach ($this->getAllSeries() as $index => $serie) {
            $localmaxlength[$index] = sizeof($serie);
            $localmaxvalue[$index] = max($serie);
            $localminvalue[$index] = min($serie);
        }

        if (is_numeric($this->_max)) {
            $maxvalue = $this->_max;
        } else {
            $maxvalue = max($localmaxvalue);
        }
        if (is_numeric($this->_min)) {
            $minvalue = $this->_min;
        } else {
            $minvalue = min($localminvalue);
        }

        // default values
        $yrange = 0;
        $yLabels = array();
        $miny = 0;
        $maxy = 0;
        $yorigin = 0;

        $maxlength = max($localmaxlength);
        if ($minvalue >= 0 && $maxvalue >= 0) {
            $miny = 0;
            if ($maxvalue > 10) {
                $p = pow(10, $this->_getPow($maxvalue));
                $maxy = (ceil($maxvalue/$p))*$p;
                $yLabels = range($miny, $maxy, $p);
            } else {
                $maxy = ceil($maxvalue+1);
                $yLabels = range($miny, $maxy, 1);
            }
            $yrange = $maxy;
            $yorigin = 0;
        }

        $chartdata = array();

        foreach ($this->getAllSeries() as $index => $serie) {
            $thisdataarray = $serie;
            $mindate = min(array_keys($serie));
            $maxdate = max(array_keys($serie)) - $mindate;
            foreach ($thisdataarray as $currentvalue => $foo) {
                $currentvalue = $currentvalue - $mindate;
                if (is_numeric($currentvalue)) {
                     $ylocation = (4095 * $currentvalue / $maxdate);
                    $firstchar = floor($ylocation / 64);
                    $secondchar = $ylocation % 64;
                    $mappedchar = substr($this->_extendedEncoding, $firstchar, 1)
                        . substr($this->_extendedEncoding, $secondchar, 1);
                    array_push($chartdata, $mappedchar . $dataDelimiter);
                } else {
                    array_push($chartdata, $dataMissing . $dataDelimiter);
                }
            }
            array_push($chartdata, $dataSetdelimiter);
            foreach ($thisdataarray as $date => $currentvalue) {
                if (is_numeric($currentvalue)) {
                    if ($yrange) {
                     $ylocation = (4095 * ($yorigin + $currentvalue) / $yrange);
                    } else {
                      $ylocation = 0;
                    }
                    $firstchar = floor($ylocation / 64);
                    $secondchar = $ylocation % 64;
                    $mappedchar = substr($this->_extendedEncoding, $firstchar, 1)
                        . substr($this->_extendedEncoding, $secondchar, 1);
                    array_push($chartdata, $mappedchar . $dataDelimiter);
                } else {
                    array_push($chartdata, $dataMissing . $dataDelimiter);
                }
            }
            array_push($chartdata, $dataSetdelimiter);
        }
        $buffer = implode('', $chartdata);

        $buffer = rtrim($buffer, $dataSetdelimiter);
        $buffer = rtrim($buffer, $dataDelimiter);
        $buffer = str_replace(($dataDelimiter . $dataSetdelimiter), $dataSetdelimiter, $buffer);

        $params['chd'] .= $buffer;

        $labelBuffer = "";
        $valueBuffer = array();
        $rangeBuffer = "";


        // chart size
        $params['chs'] = $this->getWidth().'x'.$this->getHeight();


        if (isset($deltaX) && isset($deltaY)) {
            $params['chg'] = $deltaX . ',' . $deltaY . ',1,0';
        }

        // return the encoded data
        if ($directUrl) {
            $p = array();
            foreach ($params as $name => $value) {
                $p[] = $name . '=' .urlencode($value);
            }
            return self::API_URL . '?' . implode('&', $p);
        } else {
            $gaData = urlencode(base64_encode(serialize($params)));
            $gaHash = Mage::helper('adminhtml/dashboard_data')->getChartDataHash($gaData);
            $params = array('ga' => $gaData, 'h' => $gaHash);
            return $this->getUrl('adminhtml/dashboard/tunnel', array('_query' => $params));
        }
    }

    /**
     * Return pow
     *
     * @param int $number
     * @return int
     */
    protected function _getPow($number)
    {
        $pow = 0;
        while ($number >= 10) {
            $number = $number/10;
            $pow++;
        }
        return $pow;
    }

    /**
     * Return chart width
     *
     * @return string
     */
    protected function getWidth()
    {
        return $this->_width;
    }

    /**
     * Return chart height
     *
     * @return string
     */
    protected function getHeight()
    {
        return $this->_height;
    }
}
