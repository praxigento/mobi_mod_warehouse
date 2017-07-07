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

    /** @inheritdoc */
    public function getBySaleItemId($id)
    {
        $result = [];
        $where = Entity::ATTR_SALE_ITEM_REF . '=' . (int)$id;
        $rows = $this->get($where);
        foreach ($rows as $row) {
            $item = new \Praxigento\Warehouse\Data\Entity\Quantity\Sale($row);
            $result[] = $item;
        }
        return $result;
    }
}