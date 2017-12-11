<?php

namespace Praxigento\Warehouse\Ui\DataProvider\Grid\Warehouse;

use Praxigento\Warehouse\Config as Cfg;
use Praxigento\Warehouse\Repo\Entity\Data\Warehouse as EWarehouse;

class QueryBuilder
    extends \Praxigento\Core\App\Ui\DataProvider\Grid\Query\Builder
{
    /**#@+ Tables aliases for external usage ('camelCase' naming) */
    const AS_STOCK = 'cs';
    const AS_WRHS = 'pww';
    /**#@- */

    /**#@+
     * Aliases for data attributes.
     */
    const A_CODE = 'Code';
    const A_COUNTRY_CODE = 'CountryCode';
    const A_CURRENCY = 'Currency';
    const A_ID = 'Id';
    const A_NOTE = 'Note';
    const A_WEBSITE_ID = 'WebsiteId';

    /**#@- */


    protected function getMapper()
    {
        if (is_null($this->mapper)) {
            $map = [
                self::A_ID => self::AS_STOCK . '.' . Cfg::E_CATINV_STOCK_A_STOCK_ID,
                self::A_CODE => self::AS_STOCK . '.' . Cfg::E_CATINV_STOCK_A_STOCK_NAME,
                self::A_WEBSITE_ID => self::AS_STOCK . '.' . Cfg::E_CATINV_STOCK_A_WEBSITE_ID,
                self::A_CURRENCY => self::AS_WRHS . '.' . EWarehouse::ATTR_CURRENCY,
                self::A_COUNTRY_CODE => self::AS_WRHS . '.' . EWarehouse::ATTR_COUNTRY_CODE,
                self::A_NOTE => self::AS_WRHS . '.' . EWarehouse::ATTR_NOTE
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
        $asStock = self::AS_STOCK;
        $asWrhs = self::AS_WRHS;

        /* SELECT FROM cataloginventory_stock */
        $tbl = $this->resource->getTableName(Cfg::ENTITY_MAGE_CATALOGINVENTORY_STOCK);
        $as = $asStock;
        $cols = [
            self::A_ID => Cfg::E_CATINV_STOCK_A_STOCK_ID,
            self::A_CODE => Cfg::E_CATINV_STOCK_A_STOCK_NAME,
            self::A_WEBSITE_ID => Cfg::E_CATINV_STOCK_A_WEBSITE_ID
        ];
        $result->from([$as => $tbl], $cols);

        /* LEFT JOIN prxgt_wrhs_wrhs */
        $tbl = $this->resource->getTableName(EWarehouse::ENTITY_NAME);
        $as = $asWrhs;
        $cond = $asWrhs . '.' . EWarehouse::ATTR_STOCK_REF . '=' . $asStock . '.' . Cfg::E_CATINV_STOCK_A_STOCK_ID;
        $cols = [
            self::A_CURRENCY => EWarehouse::ATTR_CURRENCY,
            self::A_COUNTRY_CODE => EWarehouse::ATTR_COUNTRY_CODE,
            self::A_NOTE => EWarehouse::ATTR_NOTE
        ];
        $result->joinLeft([$as => $tbl], $cond, $cols);
        return $result;
    }

    protected function getQueryTotal()
    {
        /* get query to select items */
        /** @var \Magento\Framework\DB\Select $result */
        $result = $this->getQueryItems();
        /* ... then replace "columns" part with own expression */
        $value = 'COUNT(' . self::AS_STOCK . '.' . Cfg::E_CATINV_STOCK_A_STOCK_ID . ')';

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