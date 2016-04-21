<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Controller\Adminhtml\Index;

use Praxigento\Warehouse\Controller\Adminhtml\Base;

class Index extends Base
{
    /**
     * @return $this
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Praxigento_Warehouse::catalog_warehouse');
        $this->_addBreadcrumb(__('Catalog'), __('Catalog'));
        $this->_addBreadcrumb(__('Inventory'), __('Inventory'));
        $this->_addBreadcrumb(__('Warehouse'), __('Warehouse'));
        return $this;
    }

    public function execute()
    {
        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend(__('QTV'));
        return $resultPage;
    }
}