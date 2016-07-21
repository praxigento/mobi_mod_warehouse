<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Plugin\Catalog\Model\ResourceModel\Product;

use Praxigento\Warehouse\Repo\Modifier\Product\Grid;

/**
 * Plugin for "\Magento\Catalog\Model\ResourceModel\Product\Collection" to enable order & filter for
 * complex attributes (qty).
 */
class Collection
{
    /** @var \Praxigento\Warehouse\Repo\Modifier\Product\Grid */
    protected $_queryModGrid;

    public function __construct(
        \Praxigento\Warehouse\Repo\Modifier\Product\Grid $queryModGrid
    ) {
        $this->_queryModGrid = $queryModGrid;
    }

    public function aroundAddFieldToFilter(
        \Magento\Catalog\Model\ResourceModel\Product\Collection $subject,
        \Closure $proceed,
        $attribute,
        $condition = null
    ) {
        if (Grid::FLD_QTY == $attribute) {
            /* use field as-is: */
            $result = $subject;
            $alias = Grid::FLD_QTY;
            $query = $result->getConnection()->prepareSqlCondition($alias, $condition);
            $result->getSelect()->where($query);
        } else {
            $result = $proceed($attribute, $condition);
        }
        return $result;
    }

    public function aroundAddOrder(
        \Magento\Catalog\Model\ResourceModel\Product\Collection $subject,
        \Closure $proceed,
        $field,
        $dir = \Magento\Framework\Data\Collection::SORT_ORDER_DESC
    ) {
        if (Grid::FLD_QTY == $field) {
            /* use field as-is: ORDER BY qty*/
            $result = $subject;
            $order = Grid::FLD_QTY . ' ' . $dir;
            $result->getSelect()->order($order);
        } else {
            $result = $proceed($field, $dir);
        }
        return $result;
    }

    /**
     * Add JOIN to warehouse data to calculate qty.
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $subject
     * @param \Closure $proceed
     */
    public function aroundGetSelectCountSql(
        \Magento\Catalog\Model\ResourceModel\Product\Collection $subject,
        \Closure $proceed
    ) {
        /** @var \Magento\Framework\DB\Select $result */
        $result = $proceed();
        $this->_queryModGrid->modifySelect($result);
        return $result;
    }
}