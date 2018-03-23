<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Repo\Data\Stock;

class Item
    extends \Praxigento\Core\App\Repo\Data\Entity\Base
{
    const A_PRICE = 'price';
    const A_STOCK_ITEM_REF = 'stock_item_ref';
    const ENTITY_NAME = 'prxgt_wrhs_stock_item';

    /**
     * @return double
     */
    public function getPrice()
    {
        $result = parent::get(self::A_PRICE);
        return $result;
    }

    /**
     * @inheritdoc
     */
    public static function getPrimaryKeyAttrs()
    {
        return [self::A_STOCK_ITEM_REF];
    }

    /**
     * @return int
     */
    public function getStockItemRef()
    {
        $result = parent::get(self::A_STOCK_ITEM_REF);
        return $result;
    }

    /**
     * @param double $data
     */
    public function setPrice($data)
    {
        parent::set(self::A_PRICE, $data);
    }

    /**
     * @param int $data
     */
    public function setStockItemRef($data)
    {
        parent::set(self::A_STOCK_ITEM_REF, $data);
    }

}