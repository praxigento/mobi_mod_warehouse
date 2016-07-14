<?php
/**
 * Repository to operate with 'Warehouse" aggregate in this module.
 *
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Repo\Agg\Def;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\ObjectManagerInterface;
use Praxigento\Core\Repo\IGeneric as IGenericRepo;
use Praxigento\Warehouse\Config as Cfg;
use Praxigento\Warehouse\Data\Agg\Warehouse as AggWarehouse;
use Praxigento\Warehouse\Data\Entity\Warehouse as EntityWarehouse;

class Warehouse
    extends \Praxigento\Core\Repo\Def\BaseCrud
    implements \Praxigento\Warehouse\Repo\Agg\IWarehouse
{

    /** @var  \Magento\Framework\DB\Adapter\AdapterInterface */
    protected $_conn;
    /** @var Warehouse\SelectFactory */
    protected $_factorySelect;
    /** @var  ObjectManagerInterface */
    protected $_manObj;
    /** @var  \Praxigento\Core\Repo\Transaction\IManager */
    protected $_manTrans;
    /** @var  \Praxigento\Warehouse\Repo\Entity\IWarehouse */
    protected $_repoEntityWarehouse;
    /** @var IGenericRepo */
    protected $_repoGeneric;
    /** @var \Magento\Framework\App\ResourceConnection */
    protected $_resource;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Core\Repo\Transaction\IManager $manTrans,
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\Repo\IGeneric $repoGeneric,
        \Praxigento\Warehouse\Repo\Entity\IWarehouse $repoEntityWarehouse,
        Warehouse\SelectFactory $factorySelect
    ) {
        $this->_manObj = $manObj;
        $this->_manTrans = $manTrans;
        $this->_resource = $resource;
        $this->_conn = $resource->getConnection();
        $this->_repoGeneric = $repoGeneric;
        $this->_repoEntityWarehouse = $repoEntityWarehouse;
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
            $tbl = Cfg::ENTITY_MAGE_CATALOGINVENTORY_STOCK;
            $stockId = $data->getId();
            if ($stockId) {
                /* lookup for catalog inventory stock by ID */
                $stockData = $this->_repoGeneric->getEntityByPk($tbl, [Cfg::E_CATINV_STOCK_A_STOCK_ID => $stockId]);
                if (!$stockData) {
                    /* create top level object (catalog inventory stock) */
                    $bind = [
                        Cfg::E_CATINV_STOCK_A_WEBSITE_ID => $data->getWebsiteId(),
                        Cfg::E_CATINV_STOCK_A_STOCK_NAME => $data->getCode()
                    ];
                    $stockId = $this->_repoGeneric->addEntity($tbl, $bind);
                }
            } else {
                /* create top level object (catalog inventory stock) */
                $bind = [
                    Cfg::E_CATINV_STOCK_A_WEBSITE_ID => $data->getWebsiteId(),
                    Cfg::E_CATINV_STOCK_A_STOCK_NAME => $data->getCode()
                ];
                $stockId = $this->_repoGeneric->addEntity($tbl, $bind);
            }
            /* then create next level object (warehouse) */
            $tbl = EntityWarehouse::ENTITY_NAME;
            $bind = [
                EntityWarehouse::ATTR_STOCK_REF => $stockId,
                EntityWarehouse::ATTR_CODE => $data->getCode(),
                EntityWarehouse::ATTR_CURRENCY => $data->getCurrency(),
                EntityWarehouse::ATTR_COUNTRY_CODE => $data->getCountryCode(),
                EntityWarehouse::ATTR_NOTE => $data->getNote()
            ];
            $this->_repoGeneric->addEntity($tbl, $bind);
            /* commit changes and compose result data object */
            $this->_manTrans->transactionCommit($trans);
            $result = $data;
            $result->setId($stockId);
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

    public function updateById($id, $data)
    {
        $bind = [
            EntityWarehouse::ATTR_CODE => $data->getData(AggWarehouse::AS_CODE),
            EntityWarehouse::ATTR_CURRENCY => $data->getData(AggWarehouse::AS_CURRENCY),
            EntityWarehouse::ATTR_COUNTRY_CODE => $data->getData(AggWarehouse::AS_COUNTRY_CODE),
            EntityWarehouse::ATTR_NOTE => $data->getData(AggWarehouse::AS_NOTE)
        ];
        $this->_repoEntityWarehouse->updateById($id, $bind);
    }
}