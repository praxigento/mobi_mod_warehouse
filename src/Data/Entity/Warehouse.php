<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Data\Entity;

use Praxigento\Core\Data\Entity\Base as EntityBase;

class Warehouse extends EntityBase
{
    const ATTR_CODE = 'code';
    const ATTR_NOTE = 'note';
    const ATTR_STOCK_REF = 'stock_ref';
    const ENTITY_NAME = 'prxgt_wrhs_warehouse';

    /**
     * @return string
     */
    public function getCode()
    {
        $result = parent::getData(self::ATTR_CODE);
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getEntityName()
    {
        return self::ENTITY_NAME;
    }

    /**
     * @return string
     */
    public function getNote()
    {
        $result = parent::getData(self::ATTR_NOTE);
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getPrimaryKeyAttrs()
    {
        return [self::ATTR_STOCK_REF];
    }

    /**
     * @return int
     */
    public function getStockRef()
    {
        $result = parent::getData(self::ATTR_STOCK_REF);
        return $result;
    }

    /**
     * @param string $data
     */
    public function setCode($data)
    {
        parent::setData(self::ATTR_CODE, $data);
    }

    /**
     * @param string $data
     */
    public function setNote($data)
    {
        parent::setData(self::ATTR_NOTE, $data);
    }

    /**
     * @param int $data
     */
    public function setStockRef($data)
    {
        parent::setData(self::ATTR_STOCK_REF, $data);
    }
}