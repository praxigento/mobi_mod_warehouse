<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Tool;

/**
 * Resolve current stock ID (MOBI-311).
 * 
 * TODO: MOBI APP IMPL (interface should be implemented on app level).
 */
interface  IStockManager
{
    public function getCurrentStockId();

    public function getStockIdByStoreId($storeId);
}