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
    private $_get;
    /** @var  \Praxigento\Core\App\Transaction\Database\IManager */
    private $_manTrans;
    /** @var \Praxigento\Warehouse\Service\QtyDistributor\Register\A\Sale\Item\Qty */
    private $_qty;

    public function __construct(
        \Praxigento\Core\App\Transaction\Database\IManager $manTrans,
        \Praxigento\Warehouse\Service\QtyDistributor\Register\A\Sale\Item\Qty $qty,
        \Praxigento\Warehouse\Repo\Query\Lots\By\Product\Id\Get $get
    )
    {
        $this->_manTrans = $manTrans;
        $this->_qty = $qty;
        $this->_get = $get;
    }

    /**
     * @param ARequest $request
     * @return AResponse
     */
    public function exec(ARequest $request)
    {
        $result = new AResponse();
        $def = $this->_manTrans->begin();
        try {
            /** @var \Praxigento\Warehouse\Service\QtyDistributor\Data\Item[] $reqItems */
            $reqItems = $request->getSaleItems();
            foreach ($reqItems as $item) {
                $itemId = $item->getItemId();
                $prodId = $item->getProductId();
                $stockId = $item->getStockId();
                $qty = $item->getQuantity();
                if ($qty > 0) {
                    /* get list of lots for the product */
                    $query = $this->_get->build();
                    $bind = [
                        AGet::BND_PROD_ID => $prodId,
                        AGet::BND_STOCK_ID => $stockId
                    ];
                    $conn = $query->getConnection();
                    $lots = $conn->fetchAll($query, $bind);
                    $this->_qty->exec($itemId, $qty, $lots);
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