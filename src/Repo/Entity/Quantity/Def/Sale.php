<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Repo\Entity\Quantity\Def;

use Magento\Framework\App\ResourceConnection;
use Praxigento\Core\Repo\Def\Entity as BaseEntityRepo;
use Praxigento\Core\Repo\IGeneric as IRepoGeneric;
use Praxigento\Warehouse\Data\Entity\Quantity\Sale as Entity;
use Praxigento\Warehouse\Repo\Entity\Quantity\ISale as IEntityRepo;

class Sale extends BaseEntityRepo implements IEntityRepo
{
    public function __construct(
        ResourceConnection $resource,
        IRepoGeneric $repoGeneric
    ) {
        parent::__construct($resource, $repoGeneric, Entity::class);
    }

    /** @inheritdoc */
    public function getBySaleItemId($id)
    {
        $result = [];
        $where = '=' . (int)$id;
        $rows = $this->get($where);
        foreach ($rows as $row) {
            $item = new Entity($row);
            $result[] = $item;
        }
        return $result;
    }
}