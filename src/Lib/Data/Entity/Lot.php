<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Lib\Data\Entity;

use Praxigento\Core\Data\Entity\Base as EntityBase;

class Lot extends EntityBase
{
    const ATTR_CODE = 'code';
    const ATTR_EXP_DATE = 'exp_date';
    const ATTR_ID = 'id';
    const ENTITY_NAME = 'prxgt_wrhs_lot';

    public function getCode()
    {
        $result = parent::getData(self::ATTR_CODE);
        return $result;
    }

    public function getEntityName()
    {
        return self::ENTITY_NAME;
    }

    public function getExpDate()
    {
        $result = parent::getData(self::ATTR_EXP_DATE);
        return $result;
    }

    public function getId()
    {
        $result = parent::getData(self::ATTR_ID);
        return $result;
    }

    public function getPrimaryKeyAttrs()
    {
        return [self::ATTR_ID];
    }

    public function setCode($data)
    {
        parent::setData(self::ATTR_CODE, $data);
    }

    public function setExpDate($data)
    {
        parent::setData(self::ATTR_EXP_DATE, $data);
    }

    public function setId($data)
    {
        parent::setData(self::ATTR_ID, $data);
    }
}