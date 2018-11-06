<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Repo\Dao;

use Praxigento\Warehouse\Repo\Data\Lot as Entity;

class Lot extends \Praxigento\Core\App\Repo\Dao
{
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\Api\App\Repo\Generic $daoGeneric
    ) {
        parent::__construct($resource, $daoGeneric, Entity::class);
    }

}