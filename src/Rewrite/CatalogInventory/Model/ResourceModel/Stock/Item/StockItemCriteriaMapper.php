<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Rewrite\CatalogInventory\Model\ResourceModel\Stock\Item;


use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\ObjectFactory;
use Magento\Framework\DB\MapperFactory;
use Magento\Framework\DB\Select;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface as Logger;
use \Praxigento\Warehouse\Data\Entity\Quantity as EntityQuantity;
use \Magento\CatalogInventory\Model\Stock\Item as EntityStockItem;

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