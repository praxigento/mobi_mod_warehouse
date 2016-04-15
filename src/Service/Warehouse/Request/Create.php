<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Service\Warehouse\Request;

use Praxigento\Core\Service\Base\Request;

class Create extends Request implements ICreate
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