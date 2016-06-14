<?php
/**
 * Resolve current stock ID (MOBI-311).
 *
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Tool;


interface  IStockManager
{
    public function getCurrentStockId();
}