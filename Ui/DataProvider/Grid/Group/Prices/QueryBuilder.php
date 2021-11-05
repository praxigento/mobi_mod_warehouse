<?php

/**
 * File creator: dmitriimakhov@gmail.com
 */

namespace Praxigento\Warehouse\Ui\DataProvider\Grid\Group\Prices;

use Praxigento\Warehouse\Config as Cfg;
use Praxigento\Warehouse\Repo\Data\Group\Price as EGroupPrice;
use Praxigento\Warehouse\Repo\Data\Stock\Item as EItem;
use Praxigento\Warehouse\Repo\Data\Warehouse as EWarehouse;


class QueryBuilder
    extends \Praxigento\Core\App\Ui\DataProvider\Grid\Query\Builder
{
    /**#@+ Tables aliases for external usage ('camelCase' naming) */
    const AS_CATALOG_INVENTORY_STOCK_ITEM = 'cist';
    const AS_CATALOG_PRODUCT_ENTITY = 'cpe';
    const AS_CUSTOMER_GROUP = 'cg';
    const AS_PROD_NAME = 'pname';
    const AS_PRXGT_WRHS_GROUP_PRICE = 'pwgp';
    const AS_PRXGT_WRHS_STOCK_ITEM = 'pwsi';
    const AS_PRXGT_WRHS_WRHS = 'pww';
    /**#@- */
    /**#@+
     * Aliases for data attributes.
     */
    const A_CURRENCY = 'wrhsCur';
    const A_CUSTOMER_GROUP_CODE = 'customerGroupCode';
    const A_GROUP_ID = 'groupId';
    const A_GROUP_PRICE = 'groupPrice';
    const A_PRICE = 'wrhsPrice';
    const A_PRODUCT_ID = 'productId';
    const A_PRODUCT_NAME = 'productName';
    const A_SKU = 'sku';
    const A_STOCK_REF = 'wrhsId';
    const A_WRHS_CODE = 'wrhsCode';

    /**#@- */

    protected function getMapper()
    {
        if (is_null($this->mapper)) {
            $map = [
                self::A_PRODUCT_ID => self::AS_CATALOG_PRODUCT_ENTITY . '.' . Cfg::E_PRODUCT_A_ENTITY_ID,
                self::A_PRODUCT_NAME => self::AS_PROD_NAME . '.' . Cfg::E_CATPROD_EAV_VARCHAR_A_VALUE,
                self::A_SKU => self::AS_CATALOG_PRODUCT_ENTITY . '.' . Cfg::E_PRODUCT_A_SKU,
                self::A_STOCK_REF => self::AS_PRXGT_WRHS_WRHS . '.' . EWarehouse::A_STOCK_REF,
                self::A_WRHS_CODE => self::AS_PRXGT_WRHS_WRHS . '.' . EWarehouse::A_CODE,
                self::A_PRICE => self::AS_PRXGT_WRHS_STOCK_ITEM . '.' . EItem::A_PRICE,
                self::A_GROUP_PRICE => self::AS_PRXGT_WRHS_GROUP_PRICE . '.' . EGroupPrice::A_PRICE,
                self::A_CURRENCY => self::AS_PRXGT_WRHS_WRHS . '.' . EWarehouse::A_CURRENCY,
                self::A_GROUP_ID => self::AS_PRXGT_WRHS_GROUP_PRICE . '.' . EGroupPrice::A_CUST_GROUP_REF,
                self::A_CUSTOMER_GROUP_CODE => self::AS_CUSTOMER_GROUP . '.' . Cfg::E_CUSTGROUP_A_CODE
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

        /* SELECT FROM catalog_product_entity */
        $tbl = $this->resource->getTableName(Cfg::ENTITY_MAGE_PRODUCT);
        $as = self::AS_CATALOG_PRODUCT_ENTITY;
        $cols = [
            self::A_PRODUCT_ID => Cfg::E_PRODUCT_A_ENTITY_ID,
            self::A_SKU => Cfg::E_PRODUCT_A_SKU
        ];
        $result->from([$as => $tbl], $cols);

        /* LEFT JOIN catalog_product_entity_varchar for productName */
        $tbl = $this->resource->getTableName(Cfg::ENTITY_MAGE_CATALOG_PRODUCT_EAV_VARCHAR);
        $as = self::AS_PROD_NAME;
        $cols = [
            self::A_PRODUCT_NAME => Cfg::E_CATPROD_EAV_VARCHAR_A_VALUE
        ];
        $cond1 = $as . '.' . Cfg::E_CATPROD_EAV_VARCHAR_A_ENTITY_ID . '=' . self::AS_CATALOG_PRODUCT_ENTITY . '.' . Cfg::E_PRODUCT_A_ENTITY_ID;
        $cond2 = $as . '.' . Cfg::E_CATPROD_EAV_VARCHAR_A_ATTRIBUTE_ID . '=73'; // product.name
        $cond3 = $as . '.' . Cfg::E_CATPROD_EAV_VARCHAR_A_STORE_ID . '=0'; // admin store
        $cond = "($cond1) AND ($cond2) AND ($cond3)";
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN cataloginventory_stock_item */
        $tbl = $this->resource->getTableName(Cfg::ENTITY_MAGE_CATALOGINVENTORY_STOCK_ITEM);
        $as = self::AS_CATALOG_INVENTORY_STOCK_ITEM;
        $cols = [
            // ref table
        ];
        $cond = $as . '.' . Cfg::E_CATINV_STOCK_ITEM_A_PROD_ID . '=' . self::AS_CATALOG_PRODUCT_ENTITY . '.' . Cfg::E_PRODUCT_A_ENTITY_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN prxgt_wrhs_wrhs */
        $tbl = $this->resource->getTableName(EWarehouse::ENTITY_NAME);
        $as = self::AS_PRXGT_WRHS_WRHS;
        $cols = [
            self::A_STOCK_REF => EWarehouse::A_STOCK_REF,
            self::A_WRHS_CODE => EWarehouse::A_CODE,
            self::A_CURRENCY => EWarehouse::A_CURRENCY
        ];
        $cond = $as . '.' . EWarehouse::A_STOCK_REF . '=' . self::AS_CATALOG_INVENTORY_STOCK_ITEM . '.' . Cfg::E_CATINV_STOCK_ITEM_A_STOCK_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN prxgt_wrhs_stock_item */
        $tbl = $this->resource->getTableName(EItem::ENTITY_NAME);
        $as = self::AS_PRXGT_WRHS_STOCK_ITEM;
        $cols = [
            self::A_PRICE => EItem::A_PRICE
        ];
        $cond = $as . '.' . EItem::A_STOCK_ITEM_REF . '=' . self::AS_CATALOG_INVENTORY_STOCK_ITEM . '.' . Cfg::E_CATINV_STOCK_ITEM_A_ITEM_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN prxgt_wrhs_group_price */
        $tbl = $this->resource->getTableName(EGroupPrice::ENTITY_NAME);
        $as = self::AS_PRXGT_WRHS_GROUP_PRICE;
        $cols = [
            self::A_GROUP_PRICE => EGroupPrice::A_PRICE,
            self::A_GROUP_ID => EGroupPrice::A_CUST_GROUP_REF
        ];
        $cond = $as . '.' . EGroupPrice::A_STOCK_ITEM_REF . '=' . self::AS_CATALOG_INVENTORY_STOCK_ITEM . '.' . Cfg::E_CATINV_STOCK_ITEM_A_ITEM_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN customer_group */
        $tbl = $this->resource->getTableName(Cfg::ENTITY_MAGE_CUSTOMER_GROUP);
        $as = self::AS_CUSTOMER_GROUP;
        $cols = [
            self::A_CUSTOMER_GROUP_CODE => Cfg::E_CUSTGROUP_A_CODE
        ];
        $cond = $as . '.' . Cfg::E_CUSTGROUP_A_ID . '=' . self::AS_PRXGT_WRHS_GROUP_PRICE . '.' . EGroupPrice::A_CUST_GROUP_REF;
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
