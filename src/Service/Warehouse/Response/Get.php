<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Service\Warehouse\Response;

use Praxigento\Core\Service\Base\Request;

class Get extends Request implements IGet
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