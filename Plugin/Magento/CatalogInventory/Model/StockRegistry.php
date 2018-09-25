<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Plugin\Magento\CatalogInventory\Model;

class StockRegistry
{
    /**
     * Disable creation for default stock item on product save.
     *
     * @param \Magento\CatalogInventory\Model\StockRegistry $subject
     * @param \Closure $proceed
     * @param string $productSku
     * @param \Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem
     * @return int
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