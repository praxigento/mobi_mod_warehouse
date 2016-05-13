<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Repo\Entity;


interface IWarehouse extends \Praxigento\Core\Repo\IEntity
{

    /**
     * @param array|\Praxigento\Warehouse\Data\Entity\Warehouse $data
     * @return \Praxigento\Warehouse\Data\Entity\Warehouse
     */
    public function create($data);
}