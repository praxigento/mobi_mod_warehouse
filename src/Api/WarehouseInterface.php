<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Api;

/**
 * Service to operate with "warehouse" entity in MOBI applications.
 * @api
 */
interface WarehouseInterface {

    /**
     * @param int $productId
     * @param int $scopeId
     *
     * @return \Praxigento\Warehouse\Api\Data\IWarehouse
     */
    public function read($wrhsId = null);

}