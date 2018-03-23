<?php
/**
 * Group stock status data by product and sum quantities. Add filter by $stockId for not admin stores.
 *
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Plugin\CatalogInventory\Api\Data;

use Magento\CatalogInventory\Model\Stock\Item as EntityStockItem;
use Magento\CatalogInventory\Model\Stock\Status as EntityStockStatus;
use Praxigento\Warehouse\Repo\Data\Quantity as EntityQty;

class StockStatusCollectionInterfaceFactory
{
    const AS_TBL_QTY = 'prxgtQty';
    const AS_TBL_STOCK_ITEM = 'prxgtCsi';
    /** @var  \Praxigento\Warehouse\Api\Helper\Stock */
    protected $hlpStockManager;
    /** @var  \Magento\Framework\App\ResourceConnection */
    protected $resource;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Warehouse\Api\Helper\Stock $hlpStockMan
    ) {
        $this->resource = $resource;
        $this->hlpStockManager = $hlpStockMan;
    }

    public function beforeCreate($subject, array $data = [])
    {
        /* we need wrap array as array again to transfer initial array to the parent's 'create' method */
        $result = [$data];
        /** @var  $query \Magento\Framework\Db\Query */
        $query = $data['query'];
        /** @var  $select \Magento\Framework\Db\Select */
        $select = $query->getSelectSql();
        /* add "SUM(prxgtQty.total) AS qty" to select */
        $select->columns([
            EntityStockStatus::KEY_QTY => 'SUM(' . self::AS_TBL_QTY . '.' . EntityQty::A_TOTAL . ')'
        ]);
        /* LEFT JOIN cataloginventory_stock_item */
        $tbl = [self::AS_TBL_STOCK_ITEM => $this->resource->getTableName(EntityStockItem::ENTITY)];
        $cols = [];
        $on = self::AS_TBL_STOCK_ITEM . '.' . EntityStockItem::PRODUCT_ID . '='
            . 'main_table.' . EntityStockStatus::PRODUCT_ID;
        $on .= ' AND ' . self::AS_TBL_STOCK_ITEM . '.' . EntityStockItem::STOCK_ID . '='
            . 'main_table.' . EntityStockStatus::STOCK_ID;
        $select->joinLeft($tbl, $on, $cols);
        /* LEFT JOIN prxgt_wrhs_qty */
        $tbl = [self::AS_TBL_QTY => $this->resource->getTableName(EntityQty::ENTITY_NAME)];
        $cols = [];
        $on = self::AS_TBL_QTY . '.' . EntityQty::A_STOCK_ITEM_REF . '='
            . self::AS_TBL_STOCK_ITEM . '.' . EntityStockItem::ITEM_ID;
        $select->joinLeft($tbl, $on, $cols);
        /* WHERE: filter by $stockId  */
        $stockId = (int)$this->hlpStockManager->getCurrentStockId();
        if ($stockId) {
            $select->where('main_table.' . EntityStockStatus::STOCK_ID . '=' . $stockId);
        }
        /* add GROUP BY 'product_id' */
        $select->group('main_table.' . EntityStockStatus::KEY_PRODUCT_ID);
        return $result;
    }
}