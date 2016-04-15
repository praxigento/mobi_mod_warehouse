<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Service\Warehouse;

use Magento\Framework\ObjectManagerInterface;
use Praxigento\Core\Data\Api\IHasId;
use Praxigento\Warehouse\Api\WarehouseInterface;

class Call implements WarehouseInterface
{
    /**
     * @var ObjectManagerInterface
     */
    protected $_manObj;
    /**
     * @var \Praxigento\Warehouse\Repo\Entity\IWarehouse
     */
    protected $_repoEntityWarehouse;

    public function __construct(
        ObjectManagerInterface $manObj,
        \Praxigento\Warehouse\Repo\Entity\IWarehouse $repoEntityWarehouse
    ) {
        $this->_manObj = $manObj;
        $this->_repoEntityWarehouse = $repoEntityWarehouse;
    }

    /**
     * @param Request\ICreate $data
     * @return Response\ICreate
     */
    public function create(Request\ICreate $data)
    {
        /** @var Response\Create $result */
        $result = $this->_manObj->create(Response\Create::class);
        $warehouse = $data->getWarehouse();
        $refEntity = $this->_repoEntityWarehouse->getRef();
        $bind = [
            $refEntity::ATTR_CODE => $warehouse->getCode(),
            $refEntity::ATTR_CURRENCY => $warehouse->getCurrency(),
            $refEntity::ATTR_NOTE => $warehouse->getNote()
        ];
        $id = $this->_repoEntityWarehouse->create($bind);
        $result->setId($id);
        $result->markSucceed();
        return $result;
    }

    public function get(IHasId $data)
    {
        // TODO: Implement get() method.
    }

}