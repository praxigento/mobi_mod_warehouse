<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Controller\Adminhtml\Warehouses;

use Praxigento\Warehouse\Config as Cfg;
use Praxigento\Warehouse\Controller\Adminhtml\Base;

class Index extends Base
{
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $aclResource = Cfg::MODULE . '::' . Cfg::ACL_CATALOG_WAREHOUSES;
        $activeMenu = Cfg::MODULE . '::' . Cfg::MENU_CATALOG_WAREHOUSES;
        $breadcrumbLabel = 'Warehouses';
        $breadcrumbTitle = 'Warehouses';
        $pageTitle = 'Warehouses';
        parent::__construct(
            $context,
            $resultPageFactory,
            $aclResource,
            $activeMenu,
            $breadcrumbLabel,
            $breadcrumbTitle,
            $pageTitle
        );
    }
}