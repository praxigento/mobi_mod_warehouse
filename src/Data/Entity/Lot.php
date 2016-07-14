<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Data\Entity;

class Lot
    extends \Praxigento\Core\Data\Entity\Base
{
    const ATTR_CODE = 'code';
    const ATTR_EXP_DATE = 'exp_date';
    const ATTR_ID = 'id';
    const ENTITY_NAME = 'prxgt_wrhs_lot';

    /**
     * @return string
     */
    public function getCode()
    {
        $result = parent::getData(self::ATTR_CODE);
        return $result;
    }

    /**
     * @return string
     */
    public function getExpDate()
    {
        $result = parent::getData(self::ATTR_EXP_DATE);
        return $result;
    }

    /**
     * @return int
     */
    public function getId()
    {
        $result = parent::getData(self::ATTR_ID);
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getPrimaryKeyAttrs()
    {
        return [self::ATTR_ID];
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
    public function setExpDate($data)
    {
        parent::setData(self::ATTR_EXP_DATE, $data);
    }

    /**
     * @param int $data
     */
    public function setId($data)
    {
        parent::setData(self::ATTR_ID, $data);
    }
}