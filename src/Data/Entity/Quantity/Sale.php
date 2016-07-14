<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Data\Entity\Quantity;

class Sale
    extends \Praxigento\Core\Data\Entity\Base
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
        $result = parent::getData(self::ATTR_LOT_REF);
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getPrimaryKeyAttrs()
    {
        return [self::ATTR_SALE_ITEM_REF, self::ATTR_LOT_REF];
    }

    /**
     * @return int
     */
    public function getSaleItemRef()
    {
        $result = parent::getData(self::ATTR_SALE_ITEM_REF);
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
    public function setSaleItemRef($data)
    {
        parent::setData(self::ATTR_SALE_ITEM_REF, $data);
    }

    /**
     * @param double $data
     */
    public function setTotal($data)
    {
        parent::setData(self::ATTR_TOTAL, $data);
    }
}