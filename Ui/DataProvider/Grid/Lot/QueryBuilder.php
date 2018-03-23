<?php

namespace Praxigento\Warehouse\Ui\DataProvider\Grid\Lot;


use Praxigento\Warehouse\Repo\Data\Lot as ELot;

class QueryBuilder
    extends \Praxigento\Core\App\Ui\DataProvider\Grid\Query\Builder
{
    /**#@+ Tables aliases for external usage ('camelCase' naming) */
    const AS_LOT = 'clt';
    /**#@- */

    /**#@+
     * Aliases for data attributes.
     */
    const A_CODE = 'code';
    const A_EXP_DATE = 'expDate';
    const A_ID = 'id';

    /**#@- */

    protected function getMapper()
    {
        if (is_null($this->mapper)) {
            $map = [
                self::A_ID => self::AS_LOT . '.' . ELot::A_ID,
                self::A_EXP_DATE => self::AS_LOT . '.' . ELot::A_EXP_DATE,
                self::A_CODE => self::AS_LOT . '.' . ELot::A_CODE
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
        $asLot = self::AS_LOT;

        /* SELECT FROM cataloginventory_stock */
        $tbl = $this->resource->getTableName(ELot::ENTITY_NAME);
        $as = $asLot;
        $cols = [
            self::A_ID => ELot::A_ID,
            self::A_EXP_DATE => ELot::A_EXP_DATE,
            self::A_CODE => ELot::A_CODE
        ];
        $result->from([$as => $tbl], $cols);

        return $result;
    }

    protected function getQueryTotal()
    {
        /* get query to select items */
        /** @var \Magento\Framework\DB\Select $result */
        $result = $this->getQueryItems();
        /* ... then replace "columns" part with own expression */
        $value = 'COUNT(' . self::AS_LOT . '.' . ELot::A_ID . ')';

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