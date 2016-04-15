<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Service\Warehouse\Request;

interface ICreate
{
    /**
     * @return \Praxigento\Warehouse\Data\Api\IWarehouse
     */
    public function getWarehouse();

    /**
     * @param \Praxigento\Warehouse\Data\Api\IWarehouse $data
     */
    public function setWarehouse($data);
}