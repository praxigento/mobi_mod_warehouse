<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Plugin\CatalogInventory\Model\Stock;

use Praxigento\Warehouse\Config as Cfg;

/**
 * Disable resolving for absent $stockId.
 */
class Item
{
    /**
     * Disable resolving for absent $stockId.
     *
     * @param \Magento\CatalogInventory\Model\Stock\Item $subject
     * @param \Closure $proceed
     * @return mixed
     */
    public function aroundGetStockId(
        \Magento\CatalogInventory\Model\Stock\Item $subject,
        \Closure $proceed
    ) {
        $result = $subject->getData(Cfg::E_CATINV_STOCK_ITEM_A_STOCK_ID);
        return $result;
    }
}