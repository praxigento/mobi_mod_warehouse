<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Repo\Data;

class Warehouse
    extends \Praxigento\Core\App\Repo\Data\Entity\Base
{
    const A_CODE = 'code';
    const A_COUNTRY_CODE = 'country_code';
    const A_CURRENCY = 'currency';
    const A_NOTE = 'note';
    const A_STOCK_REF = 'stock_ref';
    const ENTITY_NAME = 'prxgt_wrhs_wrhs';

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
    public function getCountryCode()
    {
        $result = parent::get(self::A_COUNTRY_CODE);
        return $result;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        $result = parent::get(self::A_CURRENCY);
        return $result;
    }

    /**
     * @return string
     */
    public function getNote()
    {
        $result = parent::get(self::A_NOTE);
        return $result;
    }

    /** @inheritdoc */
    public static function getPrimaryKeyAttrs()
    {
        return [self::A_STOCK_REF];
    }

    /**
     * @return int
     */
    public function getStockRef()
    {
        $result = parent::get(self::A_STOCK_REF);
        return $result;
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
    public function setCountryCode($data)
    {
        parent::set(self::A_COUNTRY_CODE, $data);
    }

    /**
     * @param string $data
     */
    public function setCurrency($data)
    {
        parent::set(self::A_CURRENCY, $data);
    }

    /**
     * @param string $data
     */
    public function setNote($data)
    {
        parent::set(self::A_NOTE, $data);
    }

    /**
     * @param int $data
     */
    public function setStockRef($data)
    {
        parent::set(self::A_STOCK_REF, $data);
    }
}