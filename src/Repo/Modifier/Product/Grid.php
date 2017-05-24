<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Repo\Modifier\Product;

use Magento\CatalogInventory\Model\Stock\Item as StockItem;
use Praxigento\Warehouse\Data\Entity\Quantity;

/**
 * Query modifier for Products grid.
 *
 * @deprecated query builders should be used (TODO)
 */
class Grid
{
    const FLD_QTY = \Magento\CatalogInventory\Api\Data\StockItemInterface::QTY;
    const TBL_STOCK_ITEM = StockItem::ENTITY;
    const TBL_WRHS_QTY = Quantity::ENTITY_NAME;
    /** @var \Magento\Framework\App\ResourceConnection */
    protected $_resource;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->_resource = $resource;
    }

    /**
     * MOBI-397
     *
     * @return string equation for qty summary.
     */
    public function getEquationQty()
    {
        $tbl = $this->_resource->getTableName(self::TBL_WRHS_QTY);
        $result = 'SUM(`' . $tbl . '`.`' . Quantity::ATTR_TOTAL . '`)';
        return $result;
    }

    /**
     * Add JOINs to original select.
     *
     * @param \Magento\Framework\DB\Select $select
     * @return \Magento\Framework\DB\Select
     */
    public function modifySelect(\Magento\Framework\DB\Select $select)
    {
        /* aliases for tables ... */
        $tblEntity = 'e'; // this is alias for 'catalog_product_entity' table
        $tblStockItem = $this->_resource->getTableName(self::TBL_STOCK_ITEM);
        $tblWrhsQty = $this->_resource->getTableName(self::TBL_WRHS_QTY);
        /* ... and fields */
        $fldStockItemProdId = StockItem::PRODUCT_ID;
        $fldStockItemId = StockItem::ITEM_ID;
        $fldEntityId = \Magento\Eav\Model\Entity::DEFAULT_ENTITY_ID_FIELD;
        $fldQty = self::FLD_QTY;
        $fldStockItemRef = Quantity::ATTR_STOCK_ITEM_REF;

        /* LEFT JOIN `cataloginventory_stock_item` */
        $on = "`$tblStockItem`.`$fldStockItemProdId`=`$tblEntity`.`$fldEntityId`";
        $fields = [];
        $select->joinLeft($tblStockItem, $on, $fields);

        /* LEFT JOIN `prxgt_wrhs_qty` */
        $on = "`$tblWrhsQty`.`$fldStockItemRef`=`$tblStockItem`.`$fldStockItemId`";
        $fields = [$fldQty => $this->getEquationQty()];
        $select->joinLeft($tblWrhsQty, $on, $fields);

        /* GROUP BY */
        $select->group("$tblEntity.$fldEntityId");
        return $select;
    }
}