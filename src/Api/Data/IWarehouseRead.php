<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Api\Data;

/**
 * Read request for Warehouse entity (from Praxigento_Warehouse module).
 */
interface IWarehouseRead extends IWarehouseCreate {
    /**
     * Warehouse ID.
     *
     * @return int
     */
    public function getId();
}