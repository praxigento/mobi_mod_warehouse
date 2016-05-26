<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Service\Customer\Sub;


use Praxigento\Warehouse\Data\Entity\Warehouse;

class Repo
{
    /** @var \Praxigento\Warehouse\Repo\Entity\IWarehouse */
    protected $_repoWrhs;

    public function __construct(
        \Praxigento\Warehouse\Repo\Entity\IWarehouse $repoWrhs
    ) {
        $this->_repoWrhs = $repoWrhs;
    }

    public function getStockId()
    {
        $result = null;
        $all = $this->_repoWrhs->get();
        if ($all) {
            $one = reset($all);
            $result = $one[Warehouse::ATTR_STOCK_REF];
        }
        return $result;
    }
}