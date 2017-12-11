<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Repo\Entity\Data;

class Quantity
    extends \Praxigento\Core\App\Repo\Data\Entity\Base
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
        $result = parent::get(self::ATTR_LOT_REF);
        return $result;
    }

    /**
     * @inheritdoc
     */
    public static function getPrimaryKeyAttrs()
    {
        return [self::ATTR_STOCK_ITEM_REF, self::ATTR_LOT_REF];
    }

    /**
     * @return int
     */
    public function getStockItemRef()
    {
        $result = parent::get(self::ATTR_STOCK_ITEM_REF);
        return $result;
    }

    /**
     * @return double
     */
    public function getTotal()
    {
        $result = parent::get(self::ATTR_TOTAL);
        return $result;
    }

    /**
     * @param int $data
     */
    public function setLotRef($data)
    {
        parent::set(self::ATTR_LOT_REF, $data);
    }

    /**
     * @param int $data
     */
    public function setStockItemRef($data)
    {
        parent::set(self::ATTR_STOCK_ITEM_REF, $data);
    }

    /**
     * @param double $data
     */
    public function setTotal($data)
    {
        parent::set(self::ATTR_TOTAL, $data);
    }
}