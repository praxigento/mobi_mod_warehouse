<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Controller\Adminhtml\Warehouses;

use Praxigento\Warehouse\Config as Cfg;

class Index
    extends \Praxigento\Warehouse\Controller\Adminhtml\Base
{
    public function __construct(
        \Magento\Backend\App\Action\Context $context
    ) {
        $aclResource = Cfg::MODULE . '::' . Cfg::ACL_CATALOG_WAREHOUSES;
        $activeMenu = Cfg::MODULE . '::' . Cfg::MENU_CATALOG_WAREHOUSES;
        $breadcrumbLabel = 'Warehouses';
        $breadcrumbTitle = 'Warehouses';
        $pageTitle = 'Warehouses';
        parent::__construct(
            $context,
            $aclResource,
            $activeMenu,
            $breadcrumbLabel,
            $breadcrumbTitle,
            $pageTitle
        );
    }
}