<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Repo\Entity\Def;

use Magento\Framework\App\ResourceConnection;
use Praxigento\Core\Repo\Def\Entity as BaseEntityRepo;
use Praxigento\Core\Repo\IGeneric;
use Praxigento\Warehouse\Data\Entity\Warehouse as Entity;
use Praxigento\Warehouse\Repo\Entity\IWarehouse as IEntityRepo;

class Warehouse extends BaseEntityRepo implements IEntityRepo
{
    public function __construct(
        ResourceConnection $resource,
        IGeneric $repoGeneric
    ) {
        parent::__construct($resource, $repoGeneric, new Entity());
    }

}