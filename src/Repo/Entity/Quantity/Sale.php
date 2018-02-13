<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Repo\Entity\Quantity;

use Praxigento\Warehouse\Repo\Entity\Data\Quantity\Sale as Entity;

class Sale extends \Praxigento\Core\App\Repo\Def\Entity
{

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\App\Repo\IGeneric $repoGeneric
    ) {
        parent::__construct($resource, $repoGeneric, Entity::class);
    }

}