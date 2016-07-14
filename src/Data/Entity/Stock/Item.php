<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Data\Entity\Stock;

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
        $result = parent::getData(self::ATTR_PRICE);
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getPrimaryKeyAttrs()
    {
        return [self::ATTR_STOCK_ITEM_REF];
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
     * @param double $data
     */
    public function setPrice($data)
    {
        parent::setData(self::ATTR_PRICE, $data);
    }

    /**
     * @param int $data
     */
    public function setStockItemRef($data)
    {
        parent::setData(self::ATTR_STOCK_ITEM_REF, $data);
    }

}