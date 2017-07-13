<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Repo\Entity\Quantity\Def;

use Praxigento\Core\Repo\Def\Entity as BaseEntityRepo;
use Praxigento\Warehouse\Data\Entity\Quantity\Sale as Entity;
use Praxigento\Warehouse\Repo\Entity\Quantity\ISale as IEntityRepo;

class Sale extends BaseEntityRepo implements IEntityRepo
{

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\Repo\IGeneric $repoGeneric
    ) {
        parent::__construct($resource, $repoGeneric, Entity::class);
    }

}