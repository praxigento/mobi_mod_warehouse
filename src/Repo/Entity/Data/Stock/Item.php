<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Repo\Entity\Data\Stock;

class Item
    extends \Praxigento\Core\Data\Entity\Base
{
    const ATTR_PRICE = 'price';
    const ATTR_STOCK_ITEM_REF = 'stock_item_ref';
    const ENTITY_NAME = 'prxgt_wrhs_stock_item';

    /**
     * @return double
     */
    public function getPrice()
    {
        $result = parent::get(self::ATTR_PRICE);
        return $result;
    }

    /**
     * @inheritdoc
     */
    public static function getPrimaryKeyAttrs()
    {
        return [self::ATTR_STOCK_ITEM_REF];
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
     * @param double $data
     */
    public function setPrice($data)
    {
        parent::set(self::ATTR_PRICE, $data);
    }

    /**
     * @param int $data
     */
    public function setStockItemRef($data)
    {
        parent::set(self::ATTR_STOCK_ITEM_REF, $data);
    }

}