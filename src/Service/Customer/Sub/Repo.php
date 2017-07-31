<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Service\Customer\Sub;


class Repo
{
    /** @var \Praxigento\Warehouse\Repo\Entity\Def\Warehouse */
    protected $_repoWrhs;

    public function __construct(
        \Praxigento\Warehouse\Repo\Entity\Def\Warehouse $repoWrhs
    ) {
        $this->_repoWrhs = $repoWrhs;
    }

    public function getStockId()
    {
        $result = null;
        $all = $this->_repoWrhs->get();
        if ($all) {
            /** @var \Praxigento\Warehouse\Data\Entity\Warehouse $one */
            $one = reset($all);
            $result = $one->getStockRef();
        }
        return $result;
    }
}