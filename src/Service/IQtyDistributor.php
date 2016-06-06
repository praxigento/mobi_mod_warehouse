<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Service;


interface IQtyDistributor
{
    /**
     * @param QtyDistributor\Request\RegisterForSaleItem $req
     * @return QtyDistributor\Response\RegisterForSaleItem
     */
    public function registerForSaleItem(QtyDistributor\Request\RegisterForSaleItem $req);

    /**
     * @param QtyDistributor\Request\RegisterSale $req
     * @return mixed
     */
    public function registerSale(QtyDistributor\Request\RegisterSale $req);
}