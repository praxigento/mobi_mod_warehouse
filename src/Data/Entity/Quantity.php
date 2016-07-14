<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Data\Entity;

class Quantity
    extends \Praxigento\Core\Data\Entity\Base
{
    const ATTR_LOT_REF = 'lot_ref';
    const ATTR_STOCK_ITEM_REF = 'stock_item_ref';
    const ATTR_TOTAL = 'total';
    const ENTITY_NAME = 'prxgt_wrhs_qty';

    /**
     * @return int
     */
    public function getLotRef()
    {
        $result = parent::getData(self::ATTR_LOT_REF);
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getPrimaryKeyAttrs()
    {
        return [self::ATTR_STOCK_ITEM_REF, self::ATTR_LOT_REF];
    }

    /**
     * @return int
     */
    public function getStockItemRef()
    {
        $result = parent::getData(self::ATTR_STOCK_ITEM_REF);
        return $result;
    }

    /**
     * @return double
     */
    public function getTotal()
    {
        $result = parent::getData(self::ATTR_TOTAL);
        return $result;
    }

    /**
     * @param int $data
     */
    public function setLotRef($data)
    {
        parent::setData(self::ATTR_LOT_REF, $data);
    }

    /**
     * @param int $data
     */
    public function setStockItemRef($data)
    {
        parent::setData(self::ATTR_STOCK_ITEM_REF, $data);
    }

    /**
     * @param double $data
     */
    public function setTotal($data)
    {
        parent::setData(self::ATTR_TOTAL, $data);
    }
}