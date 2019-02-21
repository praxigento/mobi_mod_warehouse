<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Plugin\Magento\InventoryCatalog\Model\ResourceModel;

/**
 * Add current warehouse filter to stock status data.
 */
class AddStockDataToCollection
{


    public function aroundExecute(
        \Magento\InventoryCatalog\Model\ResourceModel\AddStockDataToCollection $subject,
        \Closure $proceed,
        \Magento\Catalog\Model\ResourceModel\Product\Collection $collection,
        bool $isFilterInStock,
        int $stockId
    ) {
        $result = $proceed($collection, $isFilterInStock, $stockId);
        return $result;
    }

}