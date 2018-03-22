<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Plugin\Catalog\Model\ResourceModel\Product;

use Magento\Catalog\Api\Data\ProductAttributeInterface as AProdAttr;
use Praxigento\Warehouse\Api\Data\Catalog\Product as AWrhsProd;
use Praxigento\Warehouse\Config as Cfg;
use Praxigento\Warehouse\Repo\Data\Group\Price as EGroupPrice;
use Praxigento\Warehouse\Repo\Data\Stock\Item as EStockItem;
use Praxigento\Warehouse\Repo\Modifier\Product\Grid;

/**
 * Plugin for "\Magento\Catalog\Model\ResourceModel\Product\Collection" to enable order & filter for
 * complex attributes (qty).
 */
class Collection
{

    const AS_WRHS_GROUP_PRICE = 'prxgtWrhsGrpPrc';
    const AS_WRHS_STOCK_ITEM = 'prxgtWrhsStckItm';

    /** @var  \Magento\Framework\App\ResourceConnection */
    private $resource;
    /** @var \Magento\Framework\Config\ScopeInterface */
    private $scope;
    /** @var \Magento\Customer\Model\Session */
    private $session;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Config\ScopeInterface $scope,
        \Magento\Customer\Model\Session $session
    ) {
        $this->resource = $resource;
        $this->scope = $scope;
        $this->session = $session;
    }

    /**
     * Add warehouse price to product collection if 'price' attribute is added to select.
     * Add warehouse group price to product collection if 'special_price' attribute is added to select.
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $subject
     * @param \Closure $proceed
     * @param array $attribute
     * @param bool|string $joinType
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function aroundAddAttributeToSelect(
        \Magento\Catalog\Model\ResourceModel\Product\Collection $subject,
        \Closure $proceed,
        $attribute,
        $joinType = false
    ) {
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $result */
        $result = $proceed($attribute, $joinType);
        if ($attribute == AProdAttr::CODE_PRICE) {
            $query = $result->getSelect();
            $this->queryAddWrhsPrice($query);
        } elseif ($attribute == AProdAttr::CODE_SPECIAL_PRICE) {
            $query = $result->getSelect();
            $this->queryAddWrhsGroupPrice($query);
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
     * Remove group clause to get total count in adminhtml grid.
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
        $result->reset(\Zend_Db_Select::GROUP);
        return $result;
    }

    /**
     * @param $query
     * @return int|null|string
     */
    private function getAliasForCataloginventoryTbl($query)
    {
        $result = null;
        $from = $query->getPart(\Magento\Framework\DB\Select::FROM);
        $tbl = $this->resource->getTableName(Cfg::ENTITY_MAGE_CATALOGINVENTORY_STOCK_ITEM);
        foreach ($from as $as => $item) {
            if (
                isset($item['tableName']) &&
                $item['tableName'] == $tbl
            ) {
                $result = $as;
                break;
            }
        }
        return $result;
    }


    /**
     * Initial method to define customer group for front/adminhtml/...
     *
     * @return int
     */
    private function getCustomerGroup()
    {
        $result = $this->session->getCustomerGroupId();
        return $result;
    }

    /**
     * Add warehouse group price to query if original query contains attribute 'special_price'.
     *
     * @param $query
     */
    private function queryAddWrhsGroupPrice($query)
    {
        $asCatInv = $this->getAliasForCataloginventoryTbl($query);
        if ($asCatInv) {
            $scope = $this->scope->getCurrentScope();
            /* there is 'cataloginventory_stock_item' table - we can JOIN our tables to get warehouse group price  */
            /* don't join warehouse group prices for collection in adminhtml mode */
            if ($scope == \Magento\Framework\App\Area::AREA_FRONTEND) {
                /* LEFT JOIN prxgt_wrhs_group_price */
                $tbl = $this->resource->getTableName(EGroupPrice::ENTITY_NAME);
                $as = self::AS_WRHS_GROUP_PRICE;
                $cols = [AWrhsProd::A_PRICE_WRHS_GROUP => EGroupPrice::ATTR_PRICE];
                $cond = "$as." . EGroupPrice::ATTR_STOCK_ITEM_REF . "=$asCatInv." . Cfg::E_CATINV_STOCK_ITEM_A_ITEM_ID;
                $query->joinLeft([$as => $tbl], $cond, $cols);
                /* filter by current customer group */
                $groupId = $this->getCustomerGroup();
                $byGroup = "$as." . EGroupPrice::ATTR_CUST_GROUP_REF . '=' . (int)$groupId;
                $query->where($byGroup);
            }
        }
    }

    /**
     * Add warehouse price to query if original query contains attribute 'price'.
     *
     * @param $query
     */
    private function queryAddWrhsPrice($query)
    {
        $asCatInv = $this->getAliasForCataloginventoryTbl($query);
        if ($asCatInv) {
            /* there is 'cataloginventory_stock_item' table - we can JOIN our tables to get warehouse price  */
            /* LEFT JOIN prxgt_wrhs_stock_item */
            $tbl = $this->resource->getTableName(EStockItem::ENTITY_NAME);
            $as = self::AS_WRHS_STOCK_ITEM;
            $cols = [AWrhsProd::A_PRICE_WRHS => EStockItem::ATTR_PRICE];
            $cond = "$as." . EStockItem::ATTR_STOCK_ITEM_REF . "=$asCatInv." . Cfg::E_CATINV_STOCK_ITEM_A_ITEM_ID;
            $query->joinLeft([$as => $tbl], $cond, $cols);
        }
    }
}