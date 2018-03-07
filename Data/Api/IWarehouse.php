<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Data\Api;

/**
 * Warehouse data.
 */
interface IWarehouse
{
    /**
     * Get Warehouse code, not more then 32 chars ('DEFAULT'). Should be unique across all warehouse instances.
     *
     * @return string
     */
    public function getCode();

    /**
     * Get default currency for warehouse (EUR).
     *
     * @return string
     */
    public function getCurrency();

    /**
     * Get warehouse notes ('Default warehouse in Riga').
     *
     * @return string
     */
    public function getNote();

    /**
     * Get stock reference (warehouse ID in Magento).
     *
     * @return int
     */
    public function getStockRef();

    /**
     * Set Warehouse code, not more then 32 chars ('DEFAULT'). Should be unique across all warehouse instances.
     *
     * @param string $data
     */
    public function setCode($data);

    /**
     * Get default currency for warehouse (EUR).
     *
     * @param string $data
     */
    public function setCurrency($data);

    /**
     * Set warehouse notes ('Default warehouse in Riga').
     *
     * @param string $data
     */
    public function setNote($data);

    /**
     * Set stock reference (warehouse ID in Magento).
     *
     * @param int $data
     */
    public function setStockRef($data);
}