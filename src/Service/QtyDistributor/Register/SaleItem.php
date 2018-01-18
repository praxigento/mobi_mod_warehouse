<?php
/**
 *
 */

namespace Praxigento\Warehouse\Service\QtyDistributor\Register;

use Praxigento\Warehouse\Repo\Query\Lots\By\Product\Id\Get as AGet;
use Praxigento\Warehouse\Service\QtyDistributor\Register\SaleItem\Request as ARequest;
use Praxigento\Warehouse\Service\QtyDistributor\Register\SaleItem\Response as AResponse;

/**
 *
 */
class SaleItem
{
    /** @var \Praxigento\Warehouse\Repo\Query\Lots\By\Product\Id\Get */
    protected $_get;
    /** @var \Praxigento\Warehouse\Service\QtyDistributor\Register\A\Sale\Item\Qty */
    private $_qty;

    public function __construct(
        \Praxigento\Warehouse\Repo\Query\Lots\By\Product\Id\Get $get,
        \Praxigento\Warehouse\Service\QtyDistributor\Register\A\Sale\Item\Qty $qty
    )
    {
        $this->_get = $get;
        $this->_qty = $qty;
    }

    /**
     * @param ARequest $request
     * @return AResponse
     */
    public function exec(ARequest $request)
    {
        $result = new AResponse();
        $itemId = $request->getItemId();
        $prodId = $request->getProductId();
        $stockId = $request->getStockId();
        $qty = $request->getQuantity();
        if ($qty > 0) {
            /* get list of lots for the product */
            $query = $this->_get->build();
            $conn = $query->getConnection();
            $bind = [
                AGet::BND_PROD_ID => $prodId,
                AGet::BND_STOCK_ID => $stockId
            ];
            $lots = $conn->fetchAll($query, $bind);
            $this->_qty->exec($itemId, $qty, $lots);
        }
        $result->markSucceed();
        return $result;
    }
}