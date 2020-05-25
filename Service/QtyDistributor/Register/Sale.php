<?php
/**
 *
 */

namespace Praxigento\Warehouse\Service\QtyDistributor\Register;

use Praxigento\Warehouse\Repo\Query\Lots\By\Product\Id\Get as AGet;
use Praxigento\Warehouse\Service\QtyDistributor\Register\Sale\Request as ARequest;
use Praxigento\Warehouse\Service\QtyDistributor\Register\Sale\Response as AResponse;

class Sale
{
    /** @var \Praxigento\Warehouse\Repo\Query\Lots\By\Product\Id\Get */
    private $daoGetLots;
    /** @var \Praxigento\Warehouse\Service\QtyDistributor\Register\Sale\A\Qty */
    private $aQtyReg;

    public function __construct(
        \Praxigento\Warehouse\Repo\Query\Lots\By\Product\Id\Get $daoGetLots,
        \Praxigento\Warehouse\Service\QtyDistributor\Register\Sale\A\Qty $aQtyReg
    ) {
        $this->daoGetLots = $daoGetLots;
        $this->aQtyReg = $aQtyReg;
    }

    /**
     * @param ARequest $request
     * @return AResponse
     */
    public function exec(ARequest $request)
    {
        $result = new AResponse();
        /** @var \Praxigento\Warehouse\Service\QtyDistributor\Data\Item[] $reqItems */
        $reqItems = $request->getSaleItems();
        foreach ($reqItems as $item) {
            $itemId = $item->getItemId();
            $prodId = $item->getProductId();
            $stockId = $item->getStockId();
            $qty = $item->getQuantity();
            if ($qty > 0) {
                /* get list of lots for the product */
                $query = $this->daoGetLots->build();
                $bind = [
                    AGet::BND_PROD_ID => $prodId,
                    AGet::BND_STOCK_ID => $stockId
                ];
                $conn = $query->getConnection();
                $lots = $conn->fetchAll($query, $bind);
                $this->aQtyReg->exec($itemId, $qty, $lots);
            }
        }
        $result->markSucceed();
        return $result;
    }
}
