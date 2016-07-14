<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Tool;

/**
 * Resolve current stock ID (MOBI-311).
 */
interface  IStockManager
{
    public function getCurrentStockId();

    public function getStockIdByStoreId($storeId);
}