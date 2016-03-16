<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Api\Data\Def;


class Warehouse
    extends \Flancer32\Lib\DataObject
    implements \Praxigento\Warehouse\Api\Data\IWarehouse {

    const ID = 'Id';

    public function getId() {
        $result = $this->getData(self::ID);
        return $result;
    }

    public function setId($id) {
        $this->setData(self::ID, $id);
    }
}