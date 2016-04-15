<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Service\Warehouse\Response;

interface IGet
{
    /**
     * @return \Praxigento\Warehouse\Data\Api\IWarehouse
     */
    public function getWarehouse();

    /**
     * @param \Praxigento\Warehouse\Data\Api\IWarehouse $data
     * @return null
     */
    public function setWarehouse($data);
}