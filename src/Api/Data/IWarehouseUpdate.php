<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Api\Data;

use Praxigento\Core\Lib\Api\Data\IBase;

/**
 * Update request for Warehouse entity (from Praxigento_Warehouse module).
 */
interface IWarehouseUpdate extends IBase {
    /**
     * Warehouse code, not more then 32 chars ('DEFAULT'). Should be unique across all warehouse instances.
     *
     * @return string|null
     */
    public function getCode();

    /**
     * Warehouse notes, not more then 32 chars ('Default warehouse in Riga').
     *
     * @return string|null
     */
    public function getNote();

}