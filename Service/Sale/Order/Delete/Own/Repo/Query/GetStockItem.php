<?php
/**
 * Authors: Alex Gusev <flancer64@gmail.com>
 * Since: 2018
 */

namespace Praxigento\Warehouse\Service\Sale\Order\Delete\Own\Repo\Query;

use Magento\CatalogInventory\Api\Data\StockItemInterface as DStockItem;
use Praxigento\Warehouse\Config as Cfg;

class GetStockItem
    extends \Praxigento\Core\App\Repo\Query\Builder
{
    /** Tables aliases for external usage ('camelCase' naming) */
    const AS_STOCK_ITEM = 'stockItem';

    /** Columns/expressions aliases for external usage ('camelCase' naming) */
    const A_ITEM_ID = DStockItem::ITEM_ID;

    /** Bound variables names ('camelCase' naming) */
    const BND_PROD_ID = 'prodId';
    const BND_STOCK_ID = 'stockId';

    /** Entities are used in the query */
    const E_STOCK_ITEM = Cfg::ENTITY_MAGE_CATALOGINVENTORY_STOCK_ITEM;

    public function build(\Magento\Framework\DB\Select $source = null)
    {
        /* this is root query builder (started from SELECT) */
        $result = $this->conn->select();

        /* define tables aliases for internal usage (in this method) */
        $as = self::AS_STOCK_ITEM;

        /* FROM cataloginventory_stock_item */
        $tbl = $this->resource->getTableName(self::E_STOCK_ITEM);
        $cols = [
            self::A_ITEM_ID => DStockItem::ITEM_ID
        ];
        $result->from([$as => $tbl], $cols);

        /* query tuning */
        $byProdId = "$as." . Cfg::E_CATINV_STOCK_ITEM_A_PROD_ID . "=:" . self::BND_PROD_ID;
        $byStockId = "$as." . Cfg::E_CATINV_STOCK_ITEM_A_STOCK_ID . "=:" . self::BND_STOCK_ID;
        $result->where("($byProdId) AND ($byStockId)");

        return $result;
    }
}