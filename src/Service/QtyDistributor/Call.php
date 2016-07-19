<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Service\QtyDistributor;


class Call implements \Praxigento\Warehouse\Service\IQtyDistributor
{
    /** @var  \Praxigento\Core\Transaction\Database\IManager */
    protected $_manTrans;
    /** @var \Praxigento\Warehouse\Service\QtyDistributor\Sub\Repo */
    protected $_subRepo;

    public function __construct(
        \Praxigento\Core\Transaction\Database\IManager $manTrans,
        \Praxigento\Warehouse\Service\QtyDistributor\Sub\Repo $subRepo
    ) {
        $this->_manTrans = $manTrans;
        $this->_subRepo = $subRepo;
    }

    /** @inheritdoc */
    public function registerForSaleItem(Request\RegisterForSaleItem $req)
    {
        $result = new Response\RegisterForSaleItem();
        $itemId = $req->getItemId();
        $prodId = $req->getProductId();
        $stockId = $req->getStockId();
        $qty = $req->getQuantity();
        if ($qty > 0) {
            /* get list of lots for the product */
            $lots = $this->_subRepo->getLotsByProductId($prodId, $stockId);
            $this->_subRepo->registerSaleItemQty($itemId, $qty, $lots);
        }
        $result->markSucceed();
        return $result;
    }

    public function registerSale(Request\RegisterSale $req)
    {
        $result = new Response\RegisterSale();
        $def = $this->_manTrans->begin();
        try {
            /** @var \Praxigento\Warehouse\Service\QtyDistributor\Data\Item[] $reqItems */
            $reqItems = $req->getSaleItems();
            foreach ($reqItems as $item) {
                $itemId = $item->getItemId();
                $prodId = $item->getProductId();
                $stockId = $item->getStockId();
                $qty = $item->getQuantity();
                if ($qty > 0) {
                    /* get list of lots for the product */
                    $lots = $this->_subRepo->getLotsByProductId($prodId, $stockId);
                    $this->_subRepo->registerSaleItemQty($itemId, $qty, $lots);
                }
            }
            $this->_manTrans->commit($def);
            $result->markSucceed();
        } finally {
            // transaction will be rolled back if commit is not done (otherwise - do nothing)
            $this->_manTrans->end($def);
        }
        return $result;
    }
}