<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Data\Agg;


use Flancer32\Lib\DataObject;


class Warehouse extends DataObject
{
    const AS_CODE = 'Code';
    const AS_ID = 'Id';
    const AS_NOTE = 'Note';
    const AS_WEBSITE_ID = 'WebsiteId';

    public function getCode()
    {
        $result = parent::getData(self::AS_CODE);
        return $result;
    }

    public function getId()
    {
        $result = parent::getData(self::AS_ID);
        return $result;
    }

    public function getNote()
    {
        $result = parent::getData(self::AS_NOTE);
        return $result;
    }

    public function getWebsiteId()
    {
        $result = parent::getData(self::AS_WEBSITE_ID);
        return $result;
    }

    public function setCode($data)
    {
        parent::setData(self::AS_CODE, $data);
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