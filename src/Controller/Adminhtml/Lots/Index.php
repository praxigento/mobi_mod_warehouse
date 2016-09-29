<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Controller\Adminhtml\Lots;

use Praxigento\Warehouse\Config as Cfg;

class Index
    extends \Praxigento\Warehouse\Controller\Adminhtml\Base
{
    public function __construct(
        \Magento\Backend\App\Action\Context $context
    ) {
        $aclResource = Cfg::MODULE . '::' . Cfg::ACL_CATALOG_LOTS;
        $activeMenu = Cfg::MODULE . '::' . Cfg::MENU_CATALOG_LOTS;
        $breadcrumbLabel = 'Lots';
        $breadcrumbTitle = 'Lots';
        $pageTitle = 'Lots';
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