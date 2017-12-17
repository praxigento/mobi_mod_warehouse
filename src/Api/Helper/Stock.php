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

    public function getStockIdByStoreId($storeId);
}