<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Controller\Adminhtml\Inventory;

use Praxigento\Warehouse\Config as Cfg;

class Index
    extends \Praxigento\Core\App\Action\Back\Base
{
    public function __construct(
        \Magento\Backend\App\Action\Context $context
    )
    {
        $aclResource = Cfg::MODULE . '::' . Cfg::ACL_CATALOG_INVENTORY;
        $activeMenu = Cfg::MODULE . '::' . Cfg::MENU_CATALOG_INVENTORY;
        $breadcrumbLabel = 'Catalog Inventory';
        $breadcrumbTitle = 'Catalog Inventory';
        $pageTitle = 'Catalog Inventory';
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