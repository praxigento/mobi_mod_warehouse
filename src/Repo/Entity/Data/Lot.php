<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Repo\Entity\Data;

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
        $result = parent::get(self::ATTR_CODE);
        return $result;
    }

    /**
     * @return string
     */
    public function getExpDate()
    {
        $result = parent::get(self::ATTR_EXP_DATE);
        return $result;
    }

    /**
     * @return int
     */
    public function getId()
    {
        $result = parent::get(self::ATTR_ID);
        return $result;
    }

    /**
     * @inheritdoc
     */
    public static function getPrimaryKeyAttrs()
    {
        return [self::ATTR_ID];
    }

    /**
     * @param string $data
     */
    public function setCode($data)
    {
        parent::set(self::ATTR_CODE, $data);
    }

    /**
     * @param string $data
     */
    public function setExpDate($data)
    {
        parent::set(self::ATTR_EXP_DATE, $data);
    }

    /**
     * @param int $data
     */
    public function setId($data)
    {
        parent::set(self::ATTR_ID, $data);
    }
}