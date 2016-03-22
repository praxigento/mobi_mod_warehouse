<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Plugin\CatalogInventory\Ui\DataProvider\Product;

use Magento\CatalogInventory\Ui\DataProvider\Product\AddQuantityFieldToCollection as Subject;

class AddQuantityFieldToCollection {

    /**
     * Disable original "Quantity" field in the grid.
     *
     * @param Subject  $subject
     * @param \Closure $proceed
     */
    public function aroundAddField(
        Subject $subject,
        \Closure $proceed,
        \Magento\Catalog\Model\ResourceModel\Product\Collection $collection,
        $field,
        $alias = null
    ) {
        $fldProdId = \Magento\CatalogInventory\Model\Stock\Item::PRODUCT_ID;
        $fldEntityId = \Magento\Eav\Model\Entity::DEFAULT_ENTITY_ID_FIELD;
        $tbl = \Magento\CatalogInventory\Model\Stock\Item::ENTITY;
        $bind = "$fldProdId=$fldEntityId";
        $fields = [ 'qty' => 'SUM(qty)' ];
        //        $cond = null;
        $joinType = 'left';
        $collection->joinTable($tbl, $bind, $fields, null, $joinType);
        //        $collection->joinField(
        //            'qty',
        //            \Magento\CatalogInventory\Model\Stock\Item::ENTITY,
        //            [ 'qty' => 'SUM(qty)' ],
        //            "$fldProdId=$fldEntityId",
        //            null,
        //            'left'
        //        );
        $collection->groupByAttribute($fldEntityId);
        return;
    }
}