<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Service;

use Praxigento\Warehouse\Service\QtyDistributor\Request;
use Praxigento\Warehouse\Service\QtyDistributor\Response;

interface IQtyDistributor
{
    /**
     * @param Request\RegisterForSaleItem $req
     * @return Response\RegisterForSaleItem
     */
    public function registerForSaleItem(QtyDistributor\Request\RegisterForSaleItem $req);
}