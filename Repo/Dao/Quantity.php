<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Repo\Dao;

use Praxigento\Warehouse\Repo\Data\Quantity as Entity;

class Quantity
    extends \Praxigento\Core\App\Repo\Def\Entity
{
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\App\Repo\IGeneric $daoGeneric
    ) {
        parent::__construct($resource, $daoGeneric, Entity::class);
    }

    /**
     * Get all lots quantities for Magento stock item.
     *
     * @param int $stockItemId
     * @return Entity[]
     */
    public function getByStockItemId($stockItemId)
    {
        $where = Entity::A_STOCK_ITEM_REF . '=' . (int)$stockItemId;
        $result = $this->get($where);
        return $result;
    }
}