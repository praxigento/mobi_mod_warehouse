<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Service\Warehouse;

use Magento\Framework\ObjectManagerInterface;
use Praxigento\Core\Repo\Transaction\IManager;
use Praxigento\Warehouse\Api\WarehouseInterface;
use Praxigento\Warehouse\Data\Entity\Warehouse as EntityWarehouse;

class Call implements WarehouseInterface
{
    /** @var ObjectManagerInterface */
    protected $_manObj;
    /** @var  IManager */
    protected $_manTrans;
    /** @var \Praxigento\Warehouse\Repo\Entity\IWarehouse */
    protected $_repoEntityWarehouse;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Core\Repo\Transaction\IManager $manTrans,
        \Praxigento\Warehouse\Repo\Entity\IWarehouse $repoEntityWarehouse
    ) {
        $this->_manObj = $manObj;
        $this->_manTrans = $manTrans;
        $this->_repoEntityWarehouse = $repoEntityWarehouse;
    }

    /**
     * @inheritdoc
     */
    public function create(Request\ICreate $data)
    {
        /** @var Response\Create $result */
        $result = $this->_manObj->create(Response\Create::class);
        $tran = $this->_manTrans->transactionBegin();
        try {
            $warehouse = $data->getWarehouse();
            $bind = [
                EntityWarehouse::ATTR_STOCK_REF => $warehouse->getStockRef(),
                EntityWarehouse::ATTR_CODE => $warehouse->getCode(),
                EntityWarehouse::ATTR_CURRENCY => $warehouse->getCurrency(),
                EntityWarehouse::ATTR_NOTE => $warehouse->getNote()
            ];
            $id = $this->_repoEntityWarehouse->create($bind);
            $result->setId($id);
            $result->markSucceed();
            $this->_manTrans->transactionCommit($tran);
        } finally {
            $this->_manTrans->transactionClose($tran);
        }
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function get(Request\IGet $data)
    {
        /** @var Response\Get $result */
        $result = $this->_manObj->create(Response\Get::class);
        $id = $data->getId();
        $data = $this->_repoEntityWarehouse->getById($id);
        $result->setWarehouse($data);
        $result->markSucceed();
        return $result;
    }

}