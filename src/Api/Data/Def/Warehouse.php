<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Api\Data\Def;

use Praxigento\Warehouse\Api\Data\IWarehouseCreate;
use Praxigento\Warehouse\Api\Data\IWarehouseRead;
use Praxigento\Warehouse\Api\Data\IWarehouseUpdate;
use Praxigento\Warehouse\Lib\Entity\Warehouse as WarehouseEntity;

class Warehouse
    extends WarehouseEntity
    implements IWarehouseCreate, IWarehouseRead, IWarehouseUpdate {

}