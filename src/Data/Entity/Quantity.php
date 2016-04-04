<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Data\Entity;

use Praxigento\Core\Data\Entity\Base as EntityBase;

class Quantity extends EntityBase
{
    const ATTR_LOT_REF = 'lot_ref';
    const ATTR_STOCK_ITEM_REF = 'stock_item_ref';
    const ATTR_TOTAL = 'total';
    const ENTITY_NAME = 'prxgt_wrhs_qty';

    /**
     * @inheritdoc
     */
    public function getEntityName()
    {
        return self::ENTITY_NAME;
    }

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
}