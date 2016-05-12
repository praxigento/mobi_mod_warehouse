<?php
/**
 * Group stock status data by product and sum quantities.
 *
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Plugin\CatalogInventory\Api\Data;

use Magento\CatalogInventory\Model\Stock\Status as StockStatusEntity;

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
            StockStatusEntity::KEY_QTY => 'SUM(' . StockStatusEntity::KEY_QTY . ')'
        ]);
        /* add GROUP BY 'product_id' */
        $select->group(StockStatusEntity::KEY_PRODUCT_ID);
        return $result;
    }
}