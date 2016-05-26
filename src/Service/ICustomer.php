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
     */
    public function getCurrentStock(Customer\Request\GetCurrentStock $req);
}