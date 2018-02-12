<?php
/**
 * Authors: Alex Gusev <flancer64@gmail.com>
 * Since: 2018
 */

namespace Praxigento\Warehouse\Service\Sale\Order;

use Praxigento\Warehouse\Service\Sale\Order\Delete\Own\Repo\Query\GetStockItem as OwnQbGetStockItem;
use Praxigento\Warehouse\Service\Sale\Order\Delete\Request as ARequest;
use Praxigento\Warehouse\Service\Sale\Order\Delete\Response as AResponse;

/**
 * Load cancelled order and return products to the inventory.
 */
class Delete
{
    /** @var \Praxigento\Warehouse\Api\Helper\Stock */
    private $hlpStock;
    /** @var \Praxigento\Warehouse\Service\Sale\Order\Delete\Own\Repo\Query\GetStockItem */
    private $qbGetStockItem;
    /** \Magento\Sales\Api\OrderRepositoryInterface */
    private $repoOrder;

    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $repoOrder,
        \Praxigento\Warehouse\Api\Helper\Stock $hlpStock,
        OwnQbGetStockItem $qbGetStockItem
    ) {
        $this->repoOrder = $repoOrder;
        $this->hlpStock = $hlpStock;
        $this->qbGetStockItem = $qbGetStockItem;
    }

    /**
     * @param ARequest $request
     * @return AResponse
     * @throws \Exception
     */
    public function exec($request)
    {
        /** define local working data */
        assert($request instanceof ARequest);
        $saleId = $request->getSaleId();

        /** perform processing */
        /** @var \Magento\Sales\Api\Data\OrderInterface $sale */
        $sale = $this->repoOrder->get($saleId);
        if ($sale) {
            $storeId = $sale->getStoreId();
            $stockId = $this->hlpStock->getStockIdByStoreId($storeId);
            $items = $sale->getAllItems();
            /** @var \Magento\Sales\Api\Data\OrderItemInterface $item */
            foreach ($items as $item) {
                $prodId = $item->getProductId();
                $stockItemId = $this->getStockItemId($prodId, $stockId);
            }
        }
        /** compose result */
        $result = new AResponse();
        return $result;
    }

    private function getStockItemId($prodId, $stockId)
    {
        $query = $this->qbGetStockItem->build();
        $conn = $query->getConnection();
        $bind = [
            OwnQbGetStockItem::BND_PROD_ID => $prodId,
            OwnQbGetStockItem::BND_STOCK_ID => $stockId
        ];
        $result = $conn->fetchOne($query, $bind);
        return $result;
    }
}