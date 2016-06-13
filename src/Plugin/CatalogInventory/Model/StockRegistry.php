<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Plugin\CatalogInventory\Model;


class StockRegistry
{
    /**
     * Disable creation for default stock item on product save.
     *
     * @param Subject $subject
     * @param \Closure $proceed
     * @param $productSku
     * @param \Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem
     * @return mixed
     */
    public function aroundUpdateStockItemBySku(
        \Magento\CatalogInventory\Model\StockRegistry $subject,
        \Closure $proceed,
        $productSku,
        \Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem
    ) {
        $result = null;
        $stockId = $stockItem->getStockId();
        if ($stockId) {
            $result = $proceed($productSku, $stockItem);

        }
        return $result;
    }
}