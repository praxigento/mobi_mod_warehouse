<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Plugin\Catalog\Model\ResourceModel\Product;

use Praxigento\Warehouse\Config as Cfg;
use Praxigento\Warehouse\Repo\Modifier\Product\Grid;

/**
 * Plugin for "\Magento\Catalog\Model\ResourceModel\Product\Collection" to enable order & filter for
 * complex attributes (qty).
 */
class Collection
{
    /** @var \Praxigento\Warehouse\Repo\Query\Catalog\Model\ResourceModel\Product\Collection\GetSelectCountSql\Builder */
    protected $qbldCountSql;
    /** @var \Praxigento\Warehouse\Repo\Query\Catalog\Model\ResourceModel\Product\Collection\Group\Price\Builder */
    protected $qbldGroupPrice;
    /** @var  \Magento\Framework\App\ResourceConnection */
    protected $resource;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Warehouse\Repo\Query\Catalog\Model\ResourceModel\Product\Collection\Group\Price\Builder $qbldGroupPrice,
        \Praxigento\Warehouse\Repo\Query\Catalog\Model\ResourceModel\Product\Collection\GetSelectCountSql\Builder $qbldCountSql
    ) {
        $this->resource = $resource;
        $this->qbldGroupPrice = $qbldGroupPrice;
        $this->qbldCountSql = $qbldCountSql;
    }

    /**
     * Add warehouse prices to product collection.
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $subject
     * @param \Closure $proceed
     * @param array $attribute
     * @param bool|string $joinType
     */
    public function aroundAddAttributeToSelect(
        \Magento\Catalog\Model\ResourceModel\Product\Collection $subject,
        \Closure $proceed,
        $attribute,
        $joinType = false
    ) {
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $result */
        $result = $proceed($attribute, $joinType);
        if ($attribute == \Magento\Catalog\Api\Data\ProductAttributeInterface::CODE_PRICE) {
            $query = $result->getSelect();
            if ($this->canProcessGroupPrices($query)) {
                $this->qbldGroupPrice->build($query);
            }
        }
        return $result;
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $subject
     * @param \Closure $proceed
     * @param $attribute
     * @param null $condition
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
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
            $conn = $result->getConnection();
            $query = $conn->prepareSqlCondition($alias, $condition);
            $select = $result->getSelect();
            $select->where($query);
        } else {
            $result = $proceed($attribute, $condition);
        }
        return $result;
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $subject
     * @param \Closure $proceed
     * @param $field
     * @param string $dir
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
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
            $select = $result->getSelect();
            $select->order($order);
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
     * @return \Magento\Framework\DB\Select
     */
    public function aroundGetSelectCountSql(
        \Magento\Catalog\Model\ResourceModel\Product\Collection $subject,
        \Closure $proceed
    ) {
        /** @var \Magento\Framework\DB\Select $result */
        $result = $proceed();
        $this->qbldCountSql->build($result);
        return $result;
    }

    /**
     * Return 'true' if we need to add warehouse group prices to the collection query.
     *
     * @param \Magento\Framework\DB\Select $query
     */
    protected function canProcessGroupPrices($query)
    {
        $result = false;
        $from = $query->getPart('from');
        $tblCisi = $this->resource->getTableName(Cfg::ENTITY_MAGE_CATALOGINVENTORY_STOCK_ITEM);
        foreach ($from as $as => $item) {
            if (
                isset($item['tableName']) &&
                $item['tableName'] == $tblCisi
            ) {
                $result = false;
                break;
            }
        }
        return $result;
    }
}