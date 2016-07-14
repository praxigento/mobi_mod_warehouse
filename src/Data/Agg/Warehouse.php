<?php
/**
 * Aggregate for Warehouse data.
 *
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Data\Agg;

use Flancer32\Lib\DataObject;

class Warehouse extends DataObject
{
    /**#@+
     * Aliases for data attributes.
     */
    const AS_CODE = 'Code';
    const AS_COUNTRY_CODE = 'CountryCode';
    const AS_CURRENCY = 'Currency';
    const AS_ID = 'Id';
    const AS_NOTE = 'Note';
    const AS_WEBSITE_ID = 'WebsiteId';
    /**#@- */

    /** @return string */
    public function getCode()
    {
        $result = parent::getData(self::AS_CODE);
        return $result;
    }

    /** @return string */
    public function getCountryCode()
    {
        $result = parent::getData(self::AS_COUNTRY_CODE);
        return $result;
    }

    /** @return string */
    public function getCurrency()
    {
        $result = parent::getData(self::AS_CURRENCY);
        return $result;
    }

    /** @return int */
    public function getId()
    {
        $result = parent::getData(self::AS_ID);
        return $result;
    }

    /** @return string */
    public function getNote()
    {
        $result = parent::getData(self::AS_NOTE);
        return $result;
    }

    /** @return int */
    public function getWebsiteId()
    {
        $result = parent::getData(self::AS_WEBSITE_ID);
        return $result;
    }

    public function setCode($data)
    {
        parent::setData(self::AS_CODE, $data);
    }

    public function setCountryCode($data)
    {
        parent::setData(self::AS_COUNTRY_CODE, $data);
    }

    public function setCurrency($data)
    {
        parent::setData(self::AS_CURRENCY, $data);
    }

    public function setId($data)
    {
        parent::setData(self::AS_ID, $data);
    }

    public function setNote($data)
    {
        parent::setData(self::AS_NOTE, $data);
    }

    public function setWebsiteId($data)
    {
        parent::setData(self::AS_WEBSITE_ID, $data);
    }

}