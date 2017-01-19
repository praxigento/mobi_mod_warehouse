<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Repo\Entity\Group\Def;

use Praxigento\Warehouse\Data\Entity\Group\Price as Entity;

class Price
    extends \Praxigento\Core\Repo\Def\Entity
    implements \Praxigento\Warehouse\Repo\Entity\Group\IPrice
{
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\Repo\IGeneric $repoGeneric
    ) {
        parent::__construct($resource, $repoGeneric, Entity::class);
    }

}