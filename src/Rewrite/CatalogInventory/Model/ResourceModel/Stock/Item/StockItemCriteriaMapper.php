<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Rewrite\CatalogInventory\Model\ResourceModel\Stock\Item;


use Magento\CatalogInventory\Model\Stock\Item as EntityStockItem;
use Praxigento\Warehouse\Data\Entity\Quantity as EntityQuantity;

class StockItemCriteriaMapper extends \Magento\CatalogInventory\Model\ResourceModel\Stock\Item\StockItemCriteriaMapper
{
    const AS_TBL_QTY = 'prxgtQty';
    const AS_FLD_QTY = 'qty';

    public function mapInitialCondition()
    {
        parent::mapInitialCondition();
        $select = $this->getSelect();
        $tbl = [self::AS_TBL_QTY => $this->getTable(EntityQuantity::ENTITY_NAME)];
        $on = self::AS_TBL_QTY . '.' . EntityQuantity::ATTR_STOCK_ITEM_REF . '=main_table.' . EntityStockItem::ITEM_ID;
        $cols = [self::AS_FLD_QTY => 'SUM(' . self::AS_TBL_QTY . '.' . EntityQuantity::ATTR_TOTAL . ')'];
        $select->joinLeft(
            $tbl,
            $on,
            $cols
        );
        $select->group('main_table.' . EntityStockItem::ITEM_ID);
    }

}