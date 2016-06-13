<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Plugin\CatalogInventory\Model\Stock;

use \Praxigento\Warehouse\Config as Cfg;

class Item
{
    /** Disable resolving for absent $stockId */
    public function aroundGetStockId(
        \Magento\CatalogInventory\Model\Stock\Item $subject,
        \Closure $proceed
    ) {
        $result = $subject->getData(Cfg::E_CATINV_STOCK_ITEM_A_STOCK_ID);
        // $result = $proceed();
        return $result;
    }
}