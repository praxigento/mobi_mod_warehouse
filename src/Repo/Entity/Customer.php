<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Repo\Entity;

use Magento\Framework\App\ResourceConnection;
use Praxigento\Core\Repo\Def\Entity as BaseEntityRepo;
use Praxigento\Core\Repo\IGeneric as IRepoGeneric;
use Praxigento\Warehouse\Repo\Entity\Data\Customer as Entity;

class Customer extends BaseEntityRepo
{
    public function __construct(
        ResourceConnection $resource,
        IRepoGeneric $repoGeneric
    ) {
        parent::__construct($resource, $repoGeneric, Entity::class);
    }

    /**
     * @param array|\Praxigento\Warehouse\Repo\Entity\Data\Customer $data
     * @return \Praxigento\Warehouse\Repo\Entity\Data\Customer
     */
    public function create($data)
    {
        $result = parent::create($data);
        return $result;
    }

    /**
     * @param int $id
     * @return \Praxigento\Warehouse\Repo\Entity\Data\Customer|bool Found instance data or 'false'
     */
    public function getById($id)
    {
        $result = parent::getById($id);
        return $result;
    }

}