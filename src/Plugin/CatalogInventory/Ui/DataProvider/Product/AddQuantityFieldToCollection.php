<?php
/**
 * Join 'cataloginventory_stock_item' & 'prxgt_wrhs_qty' tables with grouping by product_id and add 'qty' as SUM of the all quantities.
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
        /** @var \Magento\Catalog\Model\ResourceModel\AbstractResource $resourceModel */
        $resourceModel = $collection->getResource();
        $select = $collection->getSelect();
        /* aliases for tables ... */
        $tblEntity = 'e'; // this is alias for 'catalog_product_entity' table
        $tblStockItem = $resourceModel->getTable(\Magento\CatalogInventory\Model\Stock\Item::ENTITY);
        $tblWrhsQty = $resourceModel->getTable(\Praxigento\Warehouse\Data\Entity\Quantity::ENTITY_NAME);
        /* ... and fields */
        $fldStockItemProdId = \Magento\CatalogInventory\Model\Stock\Item::PRODUCT_ID;
        $fldStockItemId = \Magento\CatalogInventory\Model\Stock\Item::ITEM_ID;
        $fldEntityId = \Magento\Eav\Model\Entity::DEFAULT_ENTITY_ID_FIELD;
        $fldQty = \Magento\CatalogInventory\Api\Data\StockItemInterface::QTY;
        $fldStockItemRef = \Praxigento\Warehouse\Data\Entity\Quantity::ATTR_STOCK_ITEM_REF;
        $fldTotal = \Praxigento\Warehouse\Data\Entity\Quantity::ATTR_TOTAL;

        /* LEFT JOIN `cataloginventory_stock_item` */
        $on = "`$tblStockItem`.`$fldStockItemProdId`=`$tblEntity`.`$fldEntityId`";
        $fields = [];
        $select->joinLeft($tblStockItem, $on, $fields);

        /* LEFT JOIN `prxgt_wrhs_qty` */
        $on = "`$tblWrhsQty`.`$fldStockItemRef`=`$tblStockItem`.`$fldStockItemId`";
        $fields = [$fldQty => "SUM(`$tblWrhsQty`.`$fldTotal`)"];
        $select->joinLeft($tblWrhsQty, $on, $fields);

        /* GROUP BY */
        $select->group("$tblEntity.$fldEntityId");
        return;
    }
}