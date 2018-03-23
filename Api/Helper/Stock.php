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
    /**
     * @return int
     */
    public function getCurrentStockId();

    /**
     * ID of the default stock to process regular prices in adminhtml (one stock only in the grid).
     * @return int
     */
    public function getDefaultStockId();

    /**
     * @param int $storeId
     * @return int
     */
    public function getStockIdByStoreId($storeId);

    /**
     * Currency code for store view.
     *
     * @param $storeId
     * @return string
     */
    public function getStockCurrencyByStoreId($storeId);
}