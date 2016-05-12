<?php
/**
 * Join 'cataloginventory_stock_item' table with grouping by product_id and add 'qty' as SUM of the all quantities.
 *
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Plugin\CatalogInventory\Ui\DataProvider\Product;

use Magento\CatalogInventory\Ui\DataProvider\Product\AddQuantityFieldToCollection as Subject;

class AddQuantityFieldToCollection
{

    /**
     * @param Subject $subject
     * @param \Closure $proceed
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @param $field
     * @param null $alias
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundAddField(
        Subject $subject,
        \Closure $proceed,
        \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
    ) {
        $fldProdId = \Magento\CatalogInventory\Model\Stock\Item::PRODUCT_ID;
        $fldEntityId = \Magento\Eav\Model\Entity::DEFAULT_ENTITY_ID_FIELD;
        $fldQty = \Magento\CatalogInventory\Api\Data\StockItemInterface::QTY;
        $tbl = \Magento\CatalogInventory\Model\Stock\Item::ENTITY;
        $bind = "$fldProdId=$fldEntityId";
        $fields = [$fldQty => 'SUM(' . $fldQty . ')'];
        $cond = null;
        $joinType = 'left';
        $collection->joinTable($tbl, $bind, $fields, $cond, $joinType);
        $collection->groupByAttribute($fldEntityId);
        return;
    }
}