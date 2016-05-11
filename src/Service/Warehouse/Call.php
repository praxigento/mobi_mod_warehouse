<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Service\Warehouse;

use Magento\Framework\ObjectManagerInterface;
use Praxigento\Core\Repo\ITransactionManager;
use Praxigento\Warehouse\Api\WarehouseInterface;

class Call implements WarehouseInterface
{
    /** @var ObjectManagerInterface */
    protected $_manObj;
    /** @var  ITransactionManager */
    protected $_manTrans;
    /** @var \Praxigento\Warehouse\Repo\Entity\IWarehouse */
    protected $_repoEntityWarehouse;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Core\Repo\ITransactionManager $manTrans,
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
            $refEntity = $this->_repoEntityWarehouse->getRef();
            $bind = [
                $refEntity::ATTR_STOCK_REF => $warehouse->getStockRef(),
                $refEntity::ATTR_CODE => $warehouse->getCode(),
                $refEntity::ATTR_CURRENCY => $warehouse->getCurrency(),
                $refEntity::ATTR_NOTE => $warehouse->getNote()
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
        $result = $this->_manObj->create(Response\Create::class);
        $id = $data->getId();
        $data = $this->_repoEntityWarehouse->getById($id);
        $result->setWarehouse($data);
        $result->markSucceed();
        return $result;
    }

}