<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Service\QtyDistributor;


use Praxigento\Warehouse\Service\QtyDistributor;

class Call implements \Praxigento\Warehouse\Service\IQtyDistributor
{
    /** @var \Praxigento\Warehouse\Service\QtyDistributor\Sub\Repo */
    protected $_subRepo;

    public function __construct(
        \Praxigento\Warehouse\Service\QtyDistributor\Sub\Repo $subRepo
    ) {
        $this->_subRepo = $subRepo;
    }

    /** @inheritdoc */
    public function registerForSaleItem(QtyDistributor\Request\RegisterForSaleItem $req)
    {
        $result = new Response\RegisterForSaleItem();
        $itemId = $req->getItemId();
        $prodId = $req->getProductId();
        $stockId = $req->getStockId();
        $qty = $req->getQuantity();
        /* get list of lots for the product */
        $lots = $this->_subRepo->getLotsByProductId($prodId, $stockId);
        $this->_subRepo->registerSaleItemQty($itemId, $qty, $lots);
        $result->markSucceed();
        return $result;
    }
}