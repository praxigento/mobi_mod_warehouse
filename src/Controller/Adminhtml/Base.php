<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Praxigento\Warehouse\Config as Cfg;

abstract class Base extends \Magento\Backend\App\AbstractAction
{
    public function __construct(
        Action\Context $context
    ) {
        parent::__construct($context);
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(Cfg::MODULE . '::' . Cfg::ACL_CATALOG_WAREHOUSES);
    }
}