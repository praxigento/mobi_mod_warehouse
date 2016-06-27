<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Repo\Entity\Quantity;

interface ISale extends \Praxigento\Core\Repo\IEntity
{
    /**
     * Get all lot's quantities by sale item id.
     *
     * @param int $id
     * @return \Praxigento\Warehouse\Data\Entity\Quantity\Sale[]
     */
    public function getBySaleItemId($id);
}