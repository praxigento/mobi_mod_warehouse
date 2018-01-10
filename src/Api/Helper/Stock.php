<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Api\Helper;

/**
 * Resolve current stock ID (MOBI-311). These helper should be implemented in concrete project.
 */
interface  Stock
{
    public function getCurrentStockId();

    /**
     * ID of the default stock to process regular prices in adminhtml (one stock only in the grid).
     * @return int
     */
    public function getDefaultStockId();

    public function getStockIdByStoreId($storeId);
}