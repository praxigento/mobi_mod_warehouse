<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Repo\Dao\Quantity;

use Praxigento\Warehouse\Repo\Data\Quantity\Sale as Entity;

class Sale extends \Praxigento\Core\App\Repo\Def\Entity
{

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\App\Repo\IGeneric $daoGeneric
    ) {
        parent::__construct($resource, $daoGeneric, Entity::class);
    }

}