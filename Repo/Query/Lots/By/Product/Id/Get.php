<?php

/**
 *
 */

namespace Praxigento\Warehouse\Repo\Query\Lots\By\Product\Id;

use Praxigento\Warehouse\Config as Cfg;
use Praxigento\Warehouse\Repo\Data\Lot as ELot;
use Praxigento\Warehouse\Repo\Data\Quantity as EQuantity;

class Get
    extends \Praxigento\Core\App\Repo\Query\Builder
{

    const AS_LOT = 'pwl';
    const AS_QTY = 'pwq';
    /** Tables aliases for external usage ('camelCase' naming) */
    const AS_STOCK_ITEM = 'csi';
    /** Columns/expressions aliases for external usage ('camelCase' naming) */
    const A_LOT_CODE = 'lot_code';
    const A_LOT_EXP_DATE = 'exp_date';
    const A_LOT_ID = 'lot_id';
    const A_PROD_ID = 'product_id';
    const A_QTY = 'quantity';
    const A_STOCK_ITEM_ID = 'stock_item_id';

    /** Bound variables names ('camelCase' naming) */
    const BND_PROD_ID = 'prodId';
    const BND_STOCK_ID = 'stockId';

    public function build(\Magento\Framework\DB\Select $source = null)
    {
        /* this is root query builder (started from SELECT) */
        $result = $this->conn->select();
        /* aliases and tables */
        $asStockItem = self::AS_STOCK_ITEM;
        $asQty = self::AS_QTY;
        $asLot = self::AS_LOT;

        /* FROM cataloginventory_stock_item */
        $tbl = $this->resource->getTableName(Cfg::ENTITY_MAGE_CATALOGINVENTORY_STOCK_ITEM);
        $as = $asStockItem;
        $cols = [
            self::A_STOCK_ITEM_ID => Cfg::E_CATINV_STOCK_ITEM_A_ITEM_ID
        ];
        $result->from([$as => $tbl], $cols);

        /* LEFT JOIN prxgt_wrhs_qty */
        $tbl = $this->resource->getTableName(EQuantity::ENTITY_NAME);
        $as = $asQty;
        $cols = [
            self::A_QTY => EQuantity::ATTR_TOTAL
        ];
        $cond = $as . '.' . EQuantity::ATTR_STOCK_ITEM_REF . '=' . $asStockItem . '.' . Cfg::E_CATINV_STOCK_ITEM_A_ITEM_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN prxgt_wrhs_lot */
        $tbl = $this->resource->getTableName(ELot::ENTITY_NAME);
        $as = $asLot;
        $cols = [
            self::A_LOT_ID => ELot::ATTR_ID,
            self::A_LOT_CODE => ELot::ATTR_CODE,
            self::A_LOT_EXP_DATE => ELot::ATTR_EXP_DATE
        ];
        $cond = $as . '.' . ELot::ATTR_ID . '=' . $asQty . '.' . EQuantity::ATTR_LOT_REF;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* query tuning */
        $result->where($asStockItem . '.' . Cfg::E_CATINV_STOCK_ITEM_A_PROD_ID . '=:' . self::BND_PROD_ID .
            ' AND ' . $asStockItem . '.' . Cfg::E_CATINV_STOCK_ITEM_A_STOCK_ID . '=:' . self::BND_STOCK_ID);

        /* order by */
        $order = $asLot . '.' . ELot::ATTR_EXP_DATE . ' ASC';
        $result->order($order);
        return $result;
    }
}