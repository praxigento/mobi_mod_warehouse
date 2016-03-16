<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Api\Def;

use Praxigento\Warehouse\Api\Data\Def\Warehouse as WarehouseData;
use Praxigento\Warehouse\Api\WarehouseInterface;

class Warehouse implements WarehouseInterface {

    public function read($id = null) {
        $result = new WarehouseData();
        $result->setId(43);
        return $result;
    }
}