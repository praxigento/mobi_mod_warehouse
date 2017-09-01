<?php
/**
 * Aggregate for Warehouse data.
 *
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Data\Agg;


class Warehouse
    extends \Praxigento\Core\Data
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
        $result = parent::get(self::AS_CODE);
        return $result;
    }

    /** @return string */
    public function getCountryCode()
    {
        $result = parent::get(self::AS_COUNTRY_CODE);
        return $result;
    }

    /** @return string */
    public function getCurrency()
    {
        $result = parent::get(self::AS_CURRENCY);
        return $result;
    }

    /** @return int */
    public function getId()
    {
        $result = parent::get(self::AS_ID);
        return $result;
    }

    /** @return string */
    public function getNote()
    {
        $result = parent::get(self::AS_NOTE);
        return $result;
    }

    /** @return int */
    public function getWebsiteId()
    {
        $result = parent::get(self::AS_WEBSITE_ID);
        return $result;
    }

    public function setCode($data)
    {
        parent::set(self::AS_CODE, $data);
    }

    public function setCountryCode($data)
    {
        parent::set(self::AS_COUNTRY_CODE, $data);
    }

    public function setCurrency($data)
    {
        parent::set(self::AS_CURRENCY, $data);
    }

    public function setId($data)
    {
        parent::set(self::AS_ID, $data);
    }

    public function setNote($data)
    {
        parent::set(self::AS_NOTE, $data);
    }

    public function setWebsiteId($data)
    {
        parent::set(self::AS_WEBSITE_ID, $data);
    }

}