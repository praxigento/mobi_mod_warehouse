<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Api\Def;

use Praxigento\Warehouse\Api\Data;
use Praxigento\Warehouse\Api\WarehouseInterface;
use Praxigento\Warehouse\Data\Entity\Warehouse as WarehouseEntity;

/**
 * CRUD service for Praxigento Warehouse entity.
 */
class Warehouse implements WarehouseInterface {
    /**
     * @var \Praxigento\Core\Repo\IBasic
     */
    private $_repoBasic;

    public function __construct(
        \Praxigento\Core\Repo\IBasic $repoBasic
    ) {
        $this->_repoBasic = $repoBasic;
    }

    /**
     * @inheritdoc
     */
    public function create(Data\WarehouseInterface $data) {
        $result = $this->_repoBasic->addEntity(WarehouseEntity::ENTITY_NAME, $data);
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function delete($id) {
        throw new \Exception("'Delete' operation is not implemented yet.");
    }

    /**
     * @inheritdoc
     */
    public function read($id = null) {
        $pk = [ WarehouseEntity::ATTR_STOCK_REF => $id ];
        $data = $this->_repoBasic->getEntityByPk(WarehouseEntity::ENTITY_NAME, $pk);
        $result = new Data\Def\Warehouse();
        $result->setData($data);
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function update($id, Data\WarehouseInterface $data) {
        $where = WarehouseEntity::ATTR_STOCK_REF . '=' . (int)$id;
        $updatedRows = $this->_repoBasic->updateEntity(WarehouseEntity::ENTITY_NAME, $data, $where);
        $result = ($updatedRows > 0);
        return $result;
    }
}