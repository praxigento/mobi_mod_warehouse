<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Repo\Data;

class Warehouse
    extends \Praxigento\Core\App\Repo\Data\Entity\Base
{
    const ATTR_CODE = 'code';
    const ATTR_COUNTRY_CODE = 'country_code';
    const ATTR_CURRENCY = 'currency';
    const ATTR_NOTE = 'note';
    const ATTR_STOCK_REF = 'stock_ref';
    const ENTITY_NAME = 'prxgt_wrhs_wrhs';

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
    public function getCountryCode()
    {
        $result = parent::get(self::ATTR_COUNTRY_CODE);
        return $result;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        $result = parent::get(self::ATTR_CURRENCY);
        return $result;
    }

    /**
     * @return string
     */
    public function getNote()
    {
        $result = parent::get(self::ATTR_NOTE);
        return $result;
    }

    /** @inheritdoc */
    public static function getPrimaryKeyAttrs()
    {
        return [self::ATTR_STOCK_REF];
    }

    /**
     * @return int
     */
    public function getStockRef()
    {
        $result = parent::get(self::ATTR_STOCK_REF);
        return $result;
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
    public function setCountryCode($data)
    {
        parent::set(self::ATTR_COUNTRY_CODE, $data);
    }

    /**
     * @param string $data
     */
    public function setCurrency($data)
    {
        parent::set(self::ATTR_CURRENCY, $data);
    }

    /**
     * @param string $data
     */
    public function setNote($data)
    {
        parent::set(self::ATTR_NOTE, $data);
    }

    /**
     * @param int $data
     */
    public function setStockRef($data)
    {
        parent::set(self::ATTR_STOCK_REF, $data);
    }
}