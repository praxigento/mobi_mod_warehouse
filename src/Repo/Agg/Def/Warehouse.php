<?php
/**
 * Repository to operate with 'Warehouse" aggregate in this module.
 *
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Repo\Agg\Def;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\ObjectManagerInterface;
use Praxigento\Core\Repo\Def\Aggregate as BaseAggRepo;
use Praxigento\Core\Repo\IGeneric as IGenericRepo;
use Praxigento\Core\Repo\ITransactionManager;
use Praxigento\Warehouse\Config as Cfg;
use Praxigento\Warehouse\Data\Agg\Warehouse as AggWarehouse;
use Praxigento\Warehouse\Data\Entity\Warehouse as EntityWarehouse;
use Praxigento\Warehouse\Repo\Agg\IWarehouse;

class Warehouse extends BaseAggRepo implements IWarehouse
{

    /** @var  \Magento\Framework\DB\Adapter\AdapterInterface */
    protected $_conn;
    /** @var Warehouse\SelectFactory */
    protected $_factorySelect;
    /** @var  ObjectManagerInterface */
    protected $_manObj;
    /** @var  \Praxigento\Core\Repo\ITransactionManager */
    protected $_manTrans;
    /** @var IGenericRepo */
    protected $_repoBasic;
    /** @var \Magento\Framework\App\ResourceConnection */
    protected $_resource;

    public function __construct(
        ObjectManagerInterface $manObj,
        ITransactionManager $manTrans,
        ResourceConnection $resource,
        IGenericRepo $repoGeneric,
        Warehouse\SelectFactory $factorySelect
    ) {
        $this->_manObj = $manObj;
        $this->_manTrans = $manTrans;
        $this->_resource = $resource;
        $this->_conn = $resource->getConnection();
        $this->_repoBasic = $repoGeneric;
        $this->_factorySelect = $factorySelect;
    }

    /**
     * @deprecated probably deprecated method
     */
    protected function _initAggregate($data)
    {
        /** @var  $result AggWarehouse */
        $result = $this->_manObj->create(AggWarehouse::class);
        $result->setData($data);
        return $result;
    }


    /**
     * @param AggWarehouse $data
     * @return null|AggWarehouse
     */
    public function create($data)
    {
        $result = null;
        $trans = $this->_manTrans->transactionBegin();
        try {
            /* create top level object (catalog inventory stock) */
            $tbl = Cfg::ENTITY_MAGE_CATALOGINVENTORY_STOCK;
            $bind = [
                Cfg::E_CATINV_STOCK_A_WEBSITE_ID => $data->getWebsiteId(),
                Cfg::E_CATINV_STOCK_A_STOCK_NAME => $data->getCode()
            ];
            $id = $this->_repoBasic->addEntity($tbl, $bind);
            /* then create next level object (warehouse) */
            $tbl = EntityWarehouse::ENTITY_NAME;
            $bind = [
                EntityWarehouse::ATTR_STOCK_REF => $id,
                EntityWarehouse::ATTR_CODE => $data->getCode(),
                EntityWarehouse::ATTR_CURRENCY => $data->getCurrency(),
                EntityWarehouse::ATTR_NOTE => $data->getNote()
            ];
            $this->_repoBasic->addEntity($tbl, $bind);
            /* commit changes and compose result data object */
            $this->_manTrans->transactionCommit($trans);
            $result = $data;
            $result->setId($id);
        } finally {
            $this->_manTrans->transactionClose($trans);
        }
        return $result;
    }

    public function getById($id)
    {
        /** @var  $result AggWarehouse */
        $result = null;
        $query = $this->_factorySelect->getSelectQuery();
        $query->where(static::AS_STOCK . '.' . Cfg::E_CATINV_STOCK_A_STOCK_ID . '=:id');
        $data = $this->_conn->fetchRow($query, ['id' => $id]);
        if ($data) {
            $result = $this->_manObj->create(AggLot::class);
            $result->setData($data);
        }
        return $result;
    }

    public function getQueryToSelect()
    {
        $result = $this->_factorySelect->getSelectQuery();
        return $result;
    }

    public function getQueryToSelectCount()
    {
        $result = $this->_factorySelect->getSelectCountQuery();
        return $result;
    }
}