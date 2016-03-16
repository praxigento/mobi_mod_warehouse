<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Api\Data;


interface IWarehouse {

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     *
     * @return null
     */
    public function setId($id);
}