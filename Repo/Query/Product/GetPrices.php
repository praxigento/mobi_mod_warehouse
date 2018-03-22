<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Warehouse\Repo\Query\Product;

use Praxigento\Warehouse\Config as Cfg;
use Praxigento\Warehouse\Repo\Data\Group\Price as EGroupPrice;
use Praxigento\Warehouse\Repo\Data\Stock\Item as EWrhsStockItem;

class GetPrices
    extends \Praxigento\Core\App\Repo\Query\Builder
{

    /** Tables aliases for external usage */
    const AS_STOCK_ITEM = 'cisi';
    const AS_WRHS_GROUP = 'wgp';
    const AS_WRHS_ITEM = 'wsi';

    /** Columns/expressions aliases for external usage */
    const A_WRHS_GROUP_PRICE = 'wrhsGroupPrice';
    const A_WRHS_PRICE = 'wrhsPrice';

    /** Bound variables names */
    const BND_GROUP_ID = 'groupId';
    const BND_PROD_ID = 'prodId';
    const BND_STOCK_ID = 'stockId';

    /** Entities are used in the query */
    const E_INV_ITEM = Cfg::ENTITY_MAGE_CATALOGINVENTORY_STOCK_ITEM;
    const E_WRHS_GROUP = EGroupPrice::ENTITY_NAME;
    const E_WRHS_ITEM = EWrhsStockItem::ENTITY_NAME;

    public function build(\Magento\Framework\DB\Select $source = null)
    {
        /* this is root query builder (started from SELECT) */
        $result = $this->conn->select();

        /* define tables aliases for internal usage (in this method) */
        $asStockItem = self::AS_STOCK_ITEM;
        $asWrhsGroup = self::AS_WRHS_GROUP;
        $asWrhsItem = self::AS_WRHS_ITEM;

        /* FROM cataloginventory_stock_item */
        $tbl = $this->resource->getTableName(self::E_INV_ITEM);
        $as = $asStockItem;
        $cols = [];
        $result->from([$as => $tbl], $cols);

        /* LEFT JOIN prxgt_wrhs_stock_item */
        $tbl = $this->resource->getTableName(self::E_WRHS_ITEM);
        $as = $asWrhsItem;
        $cols = [
            self::A_WRHS_PRICE => EWrhsStockItem::ATTR_PRICE
        ];
        $cond = $as . '.' . EWrhsStockItem::ATTR_STOCK_ITEM_REF . '='
            . $asStockItem . '.' . Cfg::E_CATINV_STOCK_ITEM_A_ITEM_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN prxgt_wrhs_group_price */
        $tbl = $this->resource->getTableName(self::E_WRHS_GROUP);
        $as = $asWrhsGroup;
        $cols = [
            self::A_WRHS_GROUP_PRICE => EGroupPrice::ATTR_PRICE
        ];
        $cond = $as . '.' . EGroupPrice::ATTR_STOCK_ITEM_REF . '='
            . $asStockItem . '.' . Cfg::E_CATINV_STOCK_ITEM_A_ITEM_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* query tuning */
        $byProdId = "$asStockItem." . Cfg::E_CATINV_STOCK_ITEM_A_PROD_ID . '=:' . self::BND_PROD_ID;
        $byStockId = "$asStockItem." . Cfg::E_CATINV_STOCK_ITEM_A_STOCK_ID . '=:' . self::BND_STOCK_ID;
        $byGroupId = "$asWrhsGroup." . EGroupPrice::ATTR_CUST_GROUP_REF . '=:' . self::BND_GROUP_ID;
        $result->where("($byProdId) AND ($byStockId) AND ($byGroupId)");

        return $result;
    }
}