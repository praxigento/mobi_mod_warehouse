<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Plugin\Catalog\Model;

use Praxigento\Warehouse\Config as Cfg;
use Praxigento\Warehouse\Repo\Entity\Data\Group\Price as EGroupPrice;
use Praxigento\Warehouse\Repo\Query\Catalog\Model\ResourceModel\Product\Collection\Group\Price\Builder as QBGrpPrice;

class Layer
{
    /** Aliases for tables used in query */
    const AS_CATALOGINVENTORY_STOCK_ITEM = QBGrpPrice::AS_CATALOGINVENTORY_STOCK_ITEM;
    const AS_WRHS_GROUP_PRICE = 'prxgt_wgp';

    /** Aliases for attributes used in query */
    const A_PRICE_WRHS_GROUP = Cfg::A_PROD_PRICE_WRHS_GROUP;

    /**
     * Join warehouse group prices to product collection.
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
        $asStockItem = self::AS_CATALOGINVENTORY_STOCK_ITEM;
        $asGroupPrice = self::AS_WRHS_GROUP_PRICE;
        $tblGroupPrice = [$asGroupPrice => $collection->getTable(EGroupPrice::ENTITY_NAME)];

        // LEFT JOIN prxgt_wrhs_group_price pwgp
        $on = $asGroupPrice . '.' . EGroupPrice::ATTR_STOCK_ITEM_REF . '='
            . $asStockItem . '.' . Cfg::E_CATINV_STOCK_ITEM_A_ITEM_ID;
        $on .= ' AND ' . $asGroupPrice . '.' . EGroupPrice::ATTR_CUST_GROUP_REF . '='
            . $asPriceIndex . '.' . Cfg::E_CAT_PROD_IDX_A_CUST_GROUP_ID;
        $cols = [
            self::A_PRICE_WRHS_GROUP => EGroupPrice::ATTR_PRICE
        ];
        $query->joinLeft($tblGroupPrice, $on, $cols);

        return $result;
    }
}