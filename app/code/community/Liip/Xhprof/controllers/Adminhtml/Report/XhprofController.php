<?php

class Liip_Xhprof_Adminhtml_Report_XhprofController extends Mage_Adminhtml_Controller_Report_Abstract
{
    public function _initAction()
    {
        parent::_initAction();
        $this->_addBreadcrumb(Mage::helper('liip_xhprof')->__('Xhprof'), Mage::helper('liip_xhprof')->__('Xhprof'));
        return $this;
    }

    public function indexAction()
    {
        $this->_title(Mage::helper('liip_xhprof')->__('Reports'))->_title(Mage::helper('liip_xhprof')->__('Xhprof'));

        $this->_initAction()
            ->_setActiveMenu('report/xhprof/index')
            ->_addBreadcrumb(Mage::helper('liip_xhprof')->__('Xhprof'), Mage::helper('liip_xhprof')->__('Xhprof'));

        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function runAction()
    {
        $this->_title(Mage::helper('liip_xhprof')->__('Reports'))->_title(Mage::helper('liip_xhprof')->__('Xhprof'));

        $this->_initAction()
            ->_setActiveMenu('report/xhprof/index')
            ->_addBreadcrumb(Mage::helper('liip_xhprof')->__('Xhprof'), Mage::helper('liip_xhprof')->__('Xhprof'));

        $this->renderLayout();
    }

    public function runGridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
}
