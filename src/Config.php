<?php
/**
 * Module's configuration (hard-coded).
 *
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse;

class Config extends \Praxigento\Core\Config
{
    const ACL_CATALOG_GROUP_PRICES = 'catalog_group_prices';
    const ACL_CATALOG_LOTS = 'catalog_lots';
    const ACL_CATALOG_REMNANTS = 'catalog_remnants';
    const ACL_CATALOG_WAREHOUSES = 'catalog_warehouses';
    /**
     * Additional Product attributes. They are used in query builders to get data from DB and in
     * '\Praxigento\Warehouse\Plugin\Catalog\Model\Product\Type\Price' plugin to replace original
     * price by warehouse value.
     */
    const A_PROD_PRICE_WRHS = 'prxgt_wrhs_price';
    const A_PROD_PRICE_WRHS_GROUP = 'prxgt_wrhs_price_group';
    const MENU_CATALOG_GROUP_PRICES = self::ACL_CATALOG_GROUP_PRICES;
    const MENU_CATALOG_LOTS = self::ACL_CATALOG_LOTS;
    const MENU_CATALOG_REMNANTS = self::ACL_CATALOG_REMNANTS;
    const MENU_CATALOG_WAREHOUSES = self::ACL_CATALOG_WAREHOUSES;
    const MODULE = 'Praxigento_Warehouse';
    const ROUTE_NAME_ADMIN_CATALOG = 'catalog';
}