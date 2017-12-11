<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Repo\Entity\Data\Quantity;

class Sale
    extends \Praxigento\Core\App\Repo\Data\Entity\Base
{
    const ATTR_LOT_REF = 'lot_ref';
    const ATTR_SALE_ITEM_REF = 'sale_item_ref';
    const ATTR_TOTAL = 'total';
    const ENTITY_NAME = 'prxgt_wrhs_qty_sale';

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
        return [self::ATTR_SALE_ITEM_REF, self::ATTR_LOT_REF];
    }

    /**
     * @return int
     */
    public function getSaleItemRef()
    {
        $result = parent::get(self::ATTR_SALE_ITEM_REF);
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
    public function setSaleItemRef($data)
    {
        parent::set(self::ATTR_SALE_ITEM_REF, $data);
    }

    /**
     * @param double $data
     */
    public function setTotal($data)
    {
        parent::set(self::ATTR_TOTAL, $data);
    }
}