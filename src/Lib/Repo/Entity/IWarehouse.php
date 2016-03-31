<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Lib\Repo\Entity;

use Praxigento\Warehouse\Lib\Data\Agg\Warehouse as AggWarehouse;

interface IWarehouse
{
    /**
     * @param AggWarehouse $data
     * @return AggWarehouse
     */
    public function create($data);

    /**
     * @param int $id
     * @return AggWarehouse|null
     */
    public function getById($id);
}