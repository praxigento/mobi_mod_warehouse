<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Lib\Data\Entity;

use Praxigento\Core\Lib\Entity\Base as EntityBase;

class Warehouse extends EntityBase
{
    const ATTR_CODE = 'code';
    const ATTR_NOTE = 'note';
    const ATTR_STOCK_REF = 'stock_ref';
    const ENTITY_NAME = 'prxgt_wrhs_warehouse';

    public function getCode()
    {
        $result = parent::getData(self::ATTR_CODE);
        return $result;
    }

    public function getEntityName()
    {
        return self::ENTITY_NAME;
    }

    public function getNote()
    {
        $result = parent::getData(self::ATTR_NOTE);
        return $result;
    }

    public function getPrimaryKeyAttrs()
    {
        return [self::ATTR_STOCK_REF];
    }

    public function getStockRef()
    {
        $result = parent::getData(self::ATTR_STOCK_REF);
        return $result;
    }

    public function setCode($data)
    {
        parent::setData(self::ATTR_CODE, $data);
    }

    public function setNote($data)
    {
        parent::setData(self::ATTR_NOTE, $data);
    }

    public function setStockRef($data)
    {
        parent::setData(self::ATTR_STOCK_REF, $data);
    }
}