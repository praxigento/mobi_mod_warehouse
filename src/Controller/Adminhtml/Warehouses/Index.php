<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Controller\Adminhtml\Warehouses;

use Praxigento\Warehouse\Config as Cfg;
use Praxigento\Warehouse\Controller\Adminhtml\Base;

class Index extends Base
{
    /** @var \Magento\Framework\View\Result\PageFactory */
    protected $resultPageFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /** @inheritdoc */
    protected function _isAllowed()
    {
        $result = $this->_authorization->isAllowed(Cfg::MODULE . '::' . Cfg::ACL_CATALOG_WAREHOUSES);
        return $result;
    }

    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(Cfg::MODULE . '::' . Cfg::MENU_CATALOG_WAREHOUSES);
        $this->_addBreadcrumb(__('Warehouses'), __('Warehouses'));
        $resultPage->getConfig()->getTitle()->prepend(__('Warehouses'));
        return $resultPage;
    }
}