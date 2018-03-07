<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Controller\Adminhtml\Group\Prices;

use Praxigento\Warehouse\Config as Cfg;

class Index
    extends \Praxigento\Core\App\Action\Back\Base
{
    public function __construct(
        \Magento\Backend\App\Action\Context $context
    )
    {
        $aclResource = Cfg::MODULE . '::' . Cfg::ACL_CATALOG_GROUP_PRICES;
        $activeMenu = Cfg::MODULE . '::' . Cfg::MENU_CATALOG_GROUP_PRICES;
        $breadcrumbLabel = 'Group Prices';
        $breadcrumbTitle = 'Group Prices';
        $pageTitle = 'Group Prices';
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