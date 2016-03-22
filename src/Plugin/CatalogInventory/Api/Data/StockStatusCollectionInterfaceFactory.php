<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Plugin\CatalogInventory\Api\Data;

use Magento\CatalogInventory\Api\Data\StockStatusCollectionInterfaceFactory as PluginParent;
use Magento\CatalogInventory\Model\Stock\Status as StockStatusEntity;

class StockStatusCollectionInterfaceFactory extends PluginParent {

    public function beforeCreate(PluginParent $subject, array $data = [ ]) {
        /* we need wrap array as array again to transfer initial array to the parent 'create' method */
        $result = [ $data ];
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