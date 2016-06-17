<?php
/**
 * Group stock status data by product and sum quantities. Add filter by $stockId for not admin stores.
 *
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Plugin\CatalogInventory\Api\Data;

use Magento\CatalogInventory\Model\Stock\Item as EntityStockItem;
use Magento\CatalogInventory\Model\Stock\Status as EntityStockStatus;
use Praxigento\Warehouse\Data\Entity\Quantity as EntityQty;

class StockStatusCollectionInterfaceFactory
{
    /** @var  \Praxigento\Warehouse\Tool\IStockManager */
    protected $_toolStockManager;

    public function __construct(
        \Praxigento\Warehouse\Tool\IStockManager $toolStockMan
    ) {
        $this->_toolStockManager = $toolStockMan;
    }

    const AS_TBL_QTY = 'prxgtQty';
    const AS_TBL_STOCK_ITEM = 'prxgtCsi';

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
            EntityStockStatus::KEY_QTY => 'SUM(' . self::AS_TBL_QTY . '.' . EntityQty::ATTR_TOTAL . ')'
        ]);
        /* LEFT JOIN cataloginventory_stock_item */
        $tbl = [self::AS_TBL_STOCK_ITEM => EntityStockItem::ENTITY];
        $cols = [];
        $on = self::AS_TBL_STOCK_ITEM . '.' . EntityStockItem::PRODUCT_ID . '='
            . 'main_table.' . EntityStockStatus::PRODUCT_ID;
        $on .= ' AND ' . self::AS_TBL_STOCK_ITEM . '.' . EntityStockItem::STOCK_ID . '='
            . 'main_table.' . EntityStockStatus::STOCK_ID;
        $select->joinLeft($tbl, $on, $cols);
        /* LEFT JOIN prxgt_wrhs_qty */
        $tbl = [self::AS_TBL_QTY => EntityQty::ENTITY_NAME];
        $cols = [];
        $on = self::AS_TBL_QTY . '.' . EntityQty::ATTR_STOCK_ITEM_REF . '='
            . self::AS_TBL_STOCK_ITEM . '.' . EntityStockItem::ITEM_ID;
        $select->joinLeft($tbl, $on, $cols);
        /* WHERE: filter by $stockId  */
        $stockId = (int)$this->_toolStockManager->getCurrentStockId();
        if ($stockId) {
            $select->where('main_table.' . EntityStockStatus::STOCK_ID . '=' . $stockId);
        }
        /* add GROUP BY 'product_id' */
        $select->group('main_table.' . EntityStockStatus::KEY_PRODUCT_ID);
        return $result;
    }
}