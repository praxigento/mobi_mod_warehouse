<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Repo\Data;

class Lot
    extends \Praxigento\Core\App\Repo\Data\Entity\Base
{
    const A_CODE = 'code';
    const A_EXP_DATE = 'exp_date';
    const A_ID = 'id';
    const ENTITY_NAME = 'prxgt_wrhs_lot';

    /**
     * @return string
     */
    public function getCode()
    {
        $result = parent::get(self::A_CODE);
        return $result;
    }

    /**
     * @return string
     */
    public function getExpDate()
    {
        $result = parent::get(self::A_EXP_DATE);
        return $result;
    }

    /**
     * @return int
     */
    public function getId()
    {
        $result = parent::get(self::A_ID);
        return $result;
    }

    /**
     * @inheritdoc
     */
    public static function getPrimaryKeyAttrs()
    {
        return [self::A_ID];
    }

    /**
     * @param string $data
     */
    public function setCode($data)
    {
        parent::set(self::A_CODE, $data);
    }

    /**
     * @param string $data
     */
    public function setExpDate($data)
    {
        parent::set(self::A_EXP_DATE, $data);
    }

    /**
     * @param int $data
     */
    public function setId($data)
    {
        parent::set(self::A_ID, $data);
    }
}