<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Repo\Entity\Quantity;

class Sale extends \Praxigento\Core\Repo\Def\Entity
{

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\Repo\IGeneric $repoGeneric
    ) {
        parent::__construct($resource, $repoGeneric, \Praxigento\Warehouse\Repo\Entity\Data\Quantity\Sale::class);
    }

}