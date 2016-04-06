<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Api\Data;

use Praxigento\Core\Api\Data\IBase;

/**
 * Create request for Warehouse entity (from Praxigento_Warehouse module).
 */
interface WarehouseInterface extends IBase
{
    /**
     * Warehouse code, not more then 32 chars ('DEFAULT'). Should be unique across all warehouse instances. Required.
     *
     * @return string|null
     */
    public function getCode();

    /**
     * Warehouse notes ('Default warehouse in Riga'). Required.
     *
     * @return string|null
     */
    public function getNote();

    /**
     * Stock reference. Required.
     *
     * @return int|null
     */
    public function getStockRef();

    /**
     * @param string $data
     *
     * @return null
     */
    public function setCode($data);

    /**
     * @param string $data
     *
     * @return null
     */
    public function setNote($data);

    /**
     * @param int $data
     *
     * @return null
     */
    public function setStockRef($data);
}