<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Api\Data;

use Praxigento\Core\Lib\Api\Data\IBase;

/**
 * Create request for Warehouse entity (from Praxigento_Warehouse module).
 */
interface IWarehouse extends IBase {
    /**
     * Warehouse code, not more then 32 chars ('DEFAULT'). Should be unique across all warehouse instances. Required.
     *
     * @return string|null
     */
    public function getCode();

    /**
     * Warehouse ID. Required.
     *
     * @return int|null
     */
    public function getId();

    /**
     * Warehouse notes ('Default warehouse in Riga'). Required.
     *
     * @return string|null
     */
    public function getNote();

    /**
     * @param string $data
     *
     * @return null
     */
    public function setCode($data);

    /**
     * @param int $data
     *
     * @return null
     */
    public function setId($data);

    /**
     * @param string $data
     *
     * @return null
     */
    public function setNote($data);
}