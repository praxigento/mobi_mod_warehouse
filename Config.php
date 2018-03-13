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
    const ACL_CATALOG_INVENTORY = 'catalog_inventory';
    const ACL_CATALOG_LOTS = 'catalog_lots';
    const ACL_CATALOG_WAREHOUSES = 'catalog_warehouses';

    const MENU_CATALOG_GROUP_PRICES = self::ACL_CATALOG_GROUP_PRICES;
    const MENU_CATALOG_INVENTORY = self::ACL_CATALOG_INVENTORY;
    const MENU_CATALOG_LOTS = self::ACL_CATALOG_LOTS;
    const MENU_CATALOG_WAREHOUSES = self::ACL_CATALOG_WAREHOUSES;

    const MODULE = 'Praxigento_Warehouse';
    const ROUTE_NAME_ADMIN_CATALOG = 'catalog';
}