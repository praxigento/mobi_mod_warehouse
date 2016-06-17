<?php
/**
 * Group stock status data by product and sum quantities.
 *
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Plugin\CatalogInventory\Api\Data;

use Magento\CatalogInventory\Model\Stock\Status as EntityStockStatus;
use Praxigento\Warehouse\Data\Entity\Quantity as EntityQty;

class StockStatusCollectionInterfaceFactory
{

    public function beforeCreate($subject, array $data = [])
    {
        /* we need wrap array as array again to transfer initial array to the parent's 'create' method */
        $result = [$data];
        /** @var  $query \Magento\Framework\Db\Query */
        $query = $data['query'];
        /** @var  $select \Magento\Framework\Db\Select */
        $select = $query->getSelectSql();
        /* add "SUM(qty) as qty" to select */
        $select->columns([
            EntityStockStatus::KEY_QTY => 'SUM(' . EntityQty::ATTR_TOTAL . ')'
        ]);
        /* add GROUP BY 'product_id' */
        $select->group(EntityStockStatus::KEY_PRODUCT_ID);
        return $result;
    }
}