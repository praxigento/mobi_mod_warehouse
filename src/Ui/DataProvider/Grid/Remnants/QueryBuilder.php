<?php

/**
 * File creator: makhovdmitrii@inbox.ru
 */

namespace Praxigento\Warehouse\Ui\DataProvider\Grid\Remnants;

use Praxigento\Warehouse\Config as Cfg;
use Praxigento\Warehouse\Repo\Entity\Data\Lot as ELot;
use Praxigento\Warehouse\Repo\Entity\Data\Quantity as EQuantity;
use Praxigento\Warehouse\Repo\Entity\Data\Stock\Item as EItem;
use Praxigento\Warehouse\Repo\Entity\Data\Warehouse as EWarehouse;

class QueryBuilder
    extends \Praxigento\Core\App\Ui\DataProvider\Grid\Query\Builder
{
    const AS_CATALOG_INVENTORY_STOCK_ITEM = 'cist';
    /**#@+ Tables aliases for external usage ('camelCase' naming) */
    const AS_CATALOG_PRODUCT_ENTITY = 'cpe';
    const AS_PRXGT_WRHS_LOT = 'pwl';
    const AS_PRXGT_WRHS_QTY = 'pwq';
    const AS_PRXGT_WRHS_STOCK_ITEM = 'pwsi';
    const AS_PRXGT_WRHS_WRHS = 'pww';
    /**#@- */
    const A_CURRENCY = 'wrhsCur';
    const A_EXP_DATE = 'dateExp';
    const A_LOT_CODE = 'lotCode';
    const A_PRICE = 'wrhsPrice';
    /**#@+
     * Aliases for data attributes.
     */
    const A_PRODUCT_ID = 'productId';
    const A_SKU = 'sku';
    const A_STOCK_REF = 'wrhsId';
    const A_TOTAL = 'qty';
    const A_WRHS_CODE = 'wrhsCode';

    /**#@- */


    protected function getMapper()
    {
        if (is_null($this->mapper)) {
            $map = [
                self::A_PRODUCT_ID => self::AS_CATALOG_PRODUCT_ENTITY . '.' . Cfg::E_PRODUCT_A_ENTITY_ID,
                self::A_SKU => self::AS_CATALOG_PRODUCT_ENTITY . '.' . Cfg::E_PRODUCT_A_SKU,
                self::A_STOCK_REF => self::AS_PRXGT_WRHS_WRHS . '.' . EWarehouse::ATTR_STOCK_REF,
                self::A_WRHS_CODE => self::AS_PRXGT_WRHS_WRHS . '.' . EWarehouse::ATTR_CODE,
                self::A_PRICE => self::AS_PRXGT_WRHS_STOCK_ITEM . '.' . EItem::ATTR_PRICE,
                self::A_CURRENCY => self::AS_PRXGT_WRHS_WRHS . '.' . EWarehouse::ATTR_CURRENCY,
                self::A_LOT_CODE => self::AS_PRXGT_WRHS_LOT . '.' . ELot::ATTR_CODE,
                self::A_EXP_DATE => self::AS_PRXGT_WRHS_LOT . '.' . ELot::ATTR_EXP_DATE,
                self::A_TOTAL => self::AS_PRXGT_WRHS_QTY . '.' . EQuantity::ATTR_TOTAL
            ];
            $this->mapper = new \Praxigento\Core\App\Repo\Query\Criteria\Def\Mapper($map);
        }
        $result = $this->mapper;
        return $result;
    }

    protected function getQueryItems()
    {
        $result = $this->conn->select();
        /* define tables aliases for internal usage (in this method) */
        $asCatProd = self::AS_CATALOG_PRODUCT_ENTITY;
        $asCatInvStock = self::AS_CATALOG_INVENTORY_STOCK_ITEM;
        $asPrxgtWrhWrh = self::AS_PRXGT_WRHS_WRHS;
        $asPrxgtWrhStockItem = self::AS_PRXGT_WRHS_STOCK_ITEM;
        $asPrxgtWrhQty = self::AS_PRXGT_WRHS_QTY;
        $asPrxgtWrhLot = self::AS_PRXGT_WRHS_LOT;

        /* SELECT FROM catalog_product_entity */
        $tbl = $this->resource->getTableName(Cfg::ENTITY_MAGE_PRODUCT);
        $as = $asCatProd;
        $cols = [
            self::A_PRODUCT_ID => Cfg::E_PRODUCT_A_ENTITY_ID,
            self::A_SKU => Cfg::E_PRODUCT_A_SKU
        ];
        $result->from([$as => $tbl], $cols);

        /* LEFT JOIN cataloginventory_stock_item */
        $tbl = $this->resource->getTableName(Cfg::ENTITY_MAGE_CATALOGINVENTORY_STOCK_ITEM);
        $as = $asCatInvStock;
        $cols = [
            self::A_PRODUCT_ID => Cfg::E_PRODUCT_A_ENTITY_ID
        ];
        $cond = $as . '.' . Cfg::E_CATINV_STOCK_ITEM_A_PROD_ID . '=' . $asCatProd . '.' . Cfg::E_PRODUCT_A_ENTITY_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN prxgt_wrhs_wrhs */
        $tbl = $this->resource->getTableName(EWarehouse::ENTITY_NAME);
        $as = $asPrxgtWrhWrh;
        $cols = [
            self::A_STOCK_REF => EWarehouse::ATTR_STOCK_REF,
            self::A_WRHS_CODE => EWarehouse::ATTR_CODE,
            self::A_CURRENCY => EWarehouse::ATTR_CURRENCY
        ];
        $cond = $as . '.' . EWarehouse::ATTR_STOCK_REF . '=' . $asCatInvStock . '.' . Cfg::E_CATINV_STOCK_ITEM_A_STOCK_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN prxgt_wrhs_stock_item */
        $tbl = $this->resource->getTableName(EItem::ENTITY_NAME);
        $as = $asPrxgtWrhStockItem;
        $cols = [
            self::A_PRICE => EItem::ATTR_PRICE
        ];
        $cond = $as . '.' . EItem::ATTR_STOCK_ITEM_REF . '=' . $asCatInvStock . '.' . Cfg::E_CATINV_STOCK_ITEM_A_ITEM_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN prxgt_wrhs_qty */
        $tbl = $this->resource->getTableName(EQuantity::ENTITY_NAME);
        $as = $asPrxgtWrhQty;
        $cols = [
            self::A_TOTAL => EQuantity::ATTR_TOTAL
        ];
        $cond = $as . '.' . EQuantity::ATTR_STOCK_ITEM_REF . '=' . $asCatInvStock . '.' . Cfg::E_CATINV_STOCK_ITEM_A_ITEM_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN prxgt_wrhs_lot */
        $tbl = $this->resource->getTableName(ELot::ENTITY_NAME);
        $as = $asPrxgtWrhLot;
        $cols = [
            self::A_LOT_CODE => ELot::ATTR_CODE,
            self::A_EXP_DATE => ELot::ATTR_EXP_DATE
        ];
        $cond = $as . '.' . ELot::ATTR_ID . '=' . $asPrxgtWrhQty . '.' . EQuantity::ATTR_LOT_REF;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        return $result;
    }

    protected function getQueryTotal()
    {
        /* get query to select items */
        /** @var \Magento\Framework\DB\Select $result */
        $result = $this->getQueryItems();
        /* ... then replace "columns" part with own expression */
        $value = 'COUNT(' . self::AS_CATALOG_PRODUCT_ENTITY . '.' . Cfg::E_PRODUCT_A_ENTITY_ID . ')';

        /**
         * See method \Magento\Framework\DB\Select\ColumnsRenderer::render:
         */
        /**
         * if ($column instanceof \Zend_Db_Expr) {...}
         */
        $exp = new \Praxigento\Core\App\Repo\Query\Expression($value);
        /**
         *  list($correlationName, $column, $alias) = $columnEntry;
         */
        $entry = [null, $exp, null];
        $cols = [$entry];
        $result->setPart('columns', $cols);
        return $result;
    }
}
