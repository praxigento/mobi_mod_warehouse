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
    /** @var \Magento\Framework\ObjectManagerInterface */
    protected $_manObj;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\Repo\IGeneric $repoGeneric
    ) {
        parent::__construct($resource, $repoGeneric, Entity::class);
        $this->_manObj = $manObj;
    }

    /**
     * Method to get Sale Item by Id.
     *
     * @param integer $id
     * @return \Praxigento\Warehouse\Data\Entity\Quantity\Sale[] or empty array if no data found.
     */
    public function getBySaleItemId($id)
    {
        $where = Entity::ATTR_SALE_ITEM_REF . '=' . (int)$id;
        /** @var \Praxigento\Warehouse\Data\Entity\Quantity\Sale[] $result */
        $result = $this->get($where);
        return $result;
    }
}