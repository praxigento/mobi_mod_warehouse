<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Api\Def;

use Praxigento\Warehouse\Api\Data\Def\WarehouseCreate as WarehouseData;
use Praxigento\Warehouse\Api\WarehouseInterface;
use Praxigento\Warehouse\Lib\Entity\Warehouse as WarehouseEntity;

/**
 * CRUD service for Praxigento Warehouse entity.
 */
class Warehouse implements WarehouseInterface {
    /**
     * @var \Praxigento\Core\Lib\Repo\IBasic
     */
    private $_repoBasic;

    public function __construct(
        \Praxigento\Core\Lib\Repo\IBasic $repoBasic
    ) {
        $this->_repoBasic = $repoBasic;
    }

    public function read($id = null) {
        $result = new WarehouseData();
        $result->setId(43);
        return $result;
    }

    public function update(\Praxigento\Warehouse\Api\Data\IWarehouseCreate $data) {
        $rowData = $data->getData();
        unset($rowData[WarehouseEntity::ATTR_ID]);
        $resp = $this->_repoBasic->updateEntity(WarehouseEntity::ENTITY_NAME, $rowData);
        return $resp;
    }
}