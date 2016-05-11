<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Service\Warehouse\Response;

use Praxigento\Core\Service\Base\Response;

class Get extends Response implements IGet
{

    public function getWarehouse()
    {
        $result = parent::getWarehouse();
        return $result;
    }

    public function setWarehouse($data)
    {
        parent::setWarehouse($data);
    }
}