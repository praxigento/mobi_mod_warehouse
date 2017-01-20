<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Plugin\Catalog\Model;

use Praxigento\Warehouse\Config as Cfg;

class Layer
{
    const AS_ATTR_PRICE_WRHS = 'prxgt_wrhs_group_price';
    const AS_TBL_CATALOGINVENTORY_STOCK_ITEM = 'prxgt_csi';
    /** see \Magento\CatalogInventory\Model\ResourceModel\Stock\Status::addStockDataToCollection */
    const AS_TBL_STOCK_STATUS_INDEX = 'stock_status_index';
    const AS_TBL_WRHS_GROUP_PRICE = 'prxgt_wgp';

    /**
     * Join warehouse PV data to product collection.
     *
     * @param \Magento\Catalog\Model\Layer $subject
     * @param \Closure $proceed
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @return \Magento\Catalog\Model\Layer
     */
    public function aroundPrepareProductCollection(
        \Magento\Catalog\Model\Layer $subject,
        \Closure $proceed,
        \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
    ) {
        $result = $proceed($collection);
        $query = $collection->getSelect();
        /* aliases and tables */
        $asPriceIndex = \Magento\Catalog\Model\ResourceModel\Product\Collection::INDEX_TABLE_ALIAS;
        $asStockStatus = self::AS_TBL_STOCK_STATUS_INDEX;
        $asStockItem = self::AS_TBL_CATALOGINVENTORY_STOCK_ITEM;
        $asGroupPrice = self::AS_TBL_WRHS_GROUP_PRICE;
        $tblStockItem = [$asStockItem => $collection->getTable(Cfg::ENTITY_MAGE_CATALOGINVENTORY_STOCK_ITEM)];
        $tblGroupPrice = [
            $asGroupPrice => $collection->getTable(\Praxigento\Warehouse\Data\Entity\Group\Price::ENTITY_NAME)
        ];
        /* INNER JOIN cataloginventory_stock_item AS prxgt_csi */
        $on = $asStockItem . '.' . Cfg::E_CATINV_STOCK_ITEM_A_PROD_ID . '='
            . $asStockStatus . '.' . Cfg::E_CATINV_STOCK_STATUS_A_PROD_ID;
        $on .= ' AND ' . $asStockItem . '.' . Cfg::E_CATINV_STOCK_ITEM_A_STOCK_ID . '='
            . $asStockStatus . '.' . Cfg::E_CATINV_STOCK_STATUS_A_STOCK_ID;
        $cols = [];
        $query->joinInner($tblStockItem, $on, $cols);
        // LEFT JOIN prxgt_wrhs_group_price pwgp
        $on = $asGroupPrice . '.' . \Praxigento\Warehouse\Data\Entity\Group\Price::ATTR_STOCK_ITEM_REF . '='
            . $asStockItem . '.' . Cfg::E_CATINV_STOCK_ITEM_A_ITEM_ID;
        $on .= ' AND ' . $asGroupPrice . '.' . \Praxigento\Warehouse\Data\Entity\Group\Price::ATTR_CUST_GROUP_REF . '='
            . $asPriceIndex . '.' . Cfg::E_CAT_PROD_IDX_A_CUST_GROUP_ID;
        $cols = [self::AS_ATTR_PRICE_WRHS => \Praxigento\Warehouse\Data\Entity\Group\Price::ATTR_PRICE];
        $query->joinLeft($tblGroupPrice, $on, $cols);

        return $result;
    }
}