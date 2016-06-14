<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Service;


interface ICustomer
{
    /**
     * @param Customer\Request\GetCurrentStock $req
     * @return Customer\Response\GetCurrentStock
     * @deprecated TODO remove this code; "Store to Stock" mapping is used.
     */
    public function getCurrentStock(Customer\Request\GetCurrentStock $req);
}