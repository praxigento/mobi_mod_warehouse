<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Plugin\Catalog\Model;

use Praxigento\Warehouse\Config as Cfg;
use Praxigento\Warehouse\Repo\Entity\Data\Group\Price as EWrhsGroupPrice;
use Praxigento\Warehouse\Repo\Entity\Data\Stock\Item as EWrhsStockItem;

//use Praxigento\Warehouse\Repo\Query\Catalog\Model\ResourceModel\Product\Collection\Group\Price\Builder as QBGrpPrice;

class Layer
{
    /** Aliases for tables used in query */
    const AS_CATINV_STOCK_ITEM = 'prxgt_catinv';
    const AS_WRHS_GROUP_PRICE = 'prxgt_wgp';
    const AS_WRHS_STOCK_ITEM = 'prxgt_wsi';

    /** Aliases for attributes used in query */
    const A_PRICE_WRHS = \Praxigento\Warehouse\Plugin\Catalog\Model\Product\Type\Price::A_PRICE_WRHS;
    const A_PRICE_WRHS_GROUP = \Praxigento\Warehouse\Plugin\Catalog\Model\Product\Type\Price::A_PRICE_WRHS_GROUP;

    /**
     * Join warehouse price & warehouse group prices to product collection in catalog page.
     *
     * @param \Magento\Catalog\Model\Layer $subject
     * @param \Closure $proceed
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @return \Magento\Catalog\Model\Layer
     * @throws \Zend_Db_Select_Exception
     */
    public function aroundPrepareProductCollection(
        \Magento\Catalog\Model\Layer $subject,
        \Closure $proceed,
        \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
    ) {
        $result = $proceed($collection);
        $query = $collection->getSelect();
        $resource = $collection->getResource();
        /* aliases and tables */
        list($asStockStatus, $asPrice) = $this->parseAliases($query, $resource);
        $asStockItem = self::AS_CATINV_STOCK_ITEM;

        /* LEFT JOIN cataloginventory_stock_item */
        $tbl = $collection->getTable(Cfg::ENTITY_MAGE_CATALOGINVENTORY_STOCK_ITEM);
        $as = $asStockItem;
        $cols = [];
        $byProdId = $as . '.' . Cfg::E_CATINV_STOCK_ITEM_A_PROD_ID . '='
            . $asStockStatus . '.' . Cfg::E_CATINV_STOCK_STATUS_A_PROD_ID;
        $byStockId = $as . '.' . Cfg::E_CATINV_STOCK_ITEM_A_STOCK_ID . '='
            . $asStockStatus . '.' . Cfg::E_CATINV_STOCK_STATUS_A_STOCK_ID;
        $cond = "($byProdId) AND ($byStockId)";
        $query->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN prxgt_wrhs_stock_item */
        $tbl = $collection->getTable(EWrhsStockItem::ENTITY_NAME);
        $as = self::AS_WRHS_STOCK_ITEM;
        $cols = [
            self::A_PRICE_WRHS => EWrhsStockItem::ATTR_PRICE
        ];
        $cond = $as . '.' . EWrhsStockItem::ATTR_STOCK_ITEM_REF . '='
            . $asStockItem . '.' . Cfg::E_CATINV_STOCK_ITEM_A_ITEM_ID;
        $query->joinLeft([$as => $tbl], $cond, $cols);


        /* LEFT JOIN prxgt_wrhs_group_price */
        $tbl = $collection->getTable(EWrhsGroupPrice::ENTITY_NAME);
        $as = self::AS_WRHS_GROUP_PRICE;
        $cols = [
            self::A_PRICE_WRHS_GROUP => EWrhsGroupPrice::ATTR_PRICE
        ];
        $byStockItem = $as . '.' . EWrhsGroupPrice::ATTR_STOCK_ITEM_REF . '='
            . $asStockItem . '.' . Cfg::E_CATINV_STOCK_ITEM_A_ITEM_ID;
        $byGroupId = $as . '.' . EWrhsGroupPrice::ATTR_CUST_GROUP_REF . '='
            . $asPrice . '.' . Cfg::E_CATPROD_IDX_PRICE_A_CUST_GROUP_ID;
        $cond = "($byStockItem) AND ($byGroupId)";
        $query->joinLeft([$as => $tbl], $cond, $cols);

        return $result;
    }

    /**
     * @param \Magento\Framework\DB\Select $query
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     * @return array
     * @throws \Zend_Db_Select_Exception
     */
    private function parseAliases($query, $resource)
    {
        $stock = $price = null;
        $tblStockStatus = $resource->getTable(Cfg::ENTITY_MAGE_CATALOGINVENTORY_STOCK_STATUS);
        $tblPrice = $resource->getTable(Cfg::ENTITY_MAGE_CATALOG_PRODUCT_INDEX_PRICE);
        $from = $query->getPart(\Magento\Framework\DB\Select::FROM);
        foreach ($from as $alias => $one) {
            $table = $one['tableName'];
            if ($table == $tblStockStatus) {
                $stock = $alias;
            }
            if ($table == $tblPrice) {
                $price = $alias;
            }
        }
        return [$stock, $price];
    }
}