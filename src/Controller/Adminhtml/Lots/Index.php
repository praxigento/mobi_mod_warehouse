<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Controller\Adminhtml\Lots;

use Praxigento\Warehouse\Config as Cfg;
use Praxigento\Warehouse\Controller\Adminhtml\Base;

class Index extends Base
{
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $aclResource = Cfg::MODULE . '::' . Cfg::ACL_CATALOG_LOTS;
        $activeMenu = Cfg::MODULE . '::' . Cfg::MENU_CATALOG_LOTS;
        $breadcrumbLabel = 'Lots';
        $breadcrumbTitle = 'Lots';
        $pageTitle = 'Lots';
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