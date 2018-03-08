<?php
/**
 * Repository to operate with 'Warehouse" aggregate in this module.
 *
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Repo\Agg\Def;

use Magento\Framework\ObjectManagerInterface;
use Praxigento\Core\App\Repo\IGeneric as IGenericRepo;
use Praxigento\Warehouse\Config as Cfg;
use Praxigento\Warehouse\Data\Agg\Warehouse as AggWarehouse;
use Praxigento\Warehouse\Repo\Entity\Data\Warehouse as EntityWarehouse;

/**
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Warehouse
    extends \Praxigento\Core\App\Repo\Def\Crud
    implements \Praxigento\Warehouse\Repo\Agg\IWarehouse
{

    /** @var Warehouse\SelectFactory */
    protected $_factorySelect;
    /** @var  ObjectManagerInterface */
    protected $_manObj;
    /** @var  \Praxigento\Core\Api\App\Repo\Transaction\Manager */
    protected $_manTrans;
    /** @var  \Praxigento\Warehouse\Repo\Entity\Warehouse */
    protected $_repoEntityWarehouse;
    /** @var IGenericRepo */
    protected $_repoGeneric;
    /** @var  \Magento\Framework\DB\Adapter\AdapterInterface */
    protected $conn;
    /** @var \Magento\Framework\App\ResourceConnection */
    protected $resource;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Core\Api\App\Repo\Transaction\Manager $manTrans,
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\App\Repo\IGeneric $repoGeneric,
        \Praxigento\Warehouse\Repo\Entity\Warehouse $repoEntityWarehouse,
        Warehouse\SelectFactory $factorySelect
    ) {
        $this->_manObj = $manObj;
        $this->_manTrans = $manTrans;
        $this->resource = $resource;
        $this->conn = $resource->getConnection();
        $this->_repoGeneric = $repoGeneric;
        $this->_repoEntityWarehouse = $repoEntityWarehouse;
        $this->_factorySelect = $factorySelect;
    }

    /**
     * @param AggWarehouse $data
     * @return null|AggWarehouse
     */
    public function create($data)
    {
        $result = null;
        $def = $this->_manTrans->begin();
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
            $this->_manTrans->commit($def);
            $result = $data;
            $result->setId($stockId);
        } finally {
            $this->_manTrans->end($def);
        }
        return $result;
    }

    /**
     * @inheritdoc
     *
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    public function getById($id)
    {
        /** @var  $result AggWarehouse */
        $result = null;
        $query = $this->_factorySelect->getQueryToSelect();
        $query->where(static::AS_STOCK . '.' . Cfg::E_CATINV_STOCK_A_STOCK_ID . '=:id');
        $data = $this->conn->fetchRow($query, ['id' => $id]);
        if ($data) {
            $result = $this->_manObj->create(AggLot::class);
            $result->set($data);
        }
        return $result;
    }

    public function getQueryToSelect()
    {
        $result = $this->_factorySelect->getQueryToSelect();
        return $result;
    }

    public function getQueryToSelectCount()
    {
        $result = $this->_factorySelect->getQueryToSelectCount();
        return $result;
    }

    /**
     * @param int $id
     * @param array|\Praxigento\Core\Data $data
     * @return null
     */
    public function updateById($id, $data)
    {
        $def = $this->_manTrans->begin();
        try {
            /* update catalog inventory stock by ID */
            $tbl = Cfg::ENTITY_MAGE_CATALOGINVENTORY_STOCK;
            $bindStock = [
                Cfg::E_CATINV_STOCK_A_WEBSITE_ID => $data->getWebsiteId(),
                Cfg::E_CATINV_STOCK_A_STOCK_NAME => $data->getCode()
            ];
            $idStock = [Cfg::E_CATINV_STOCK_A_STOCK_ID => $id];
            $this->_repoGeneric->updateEntityById($tbl, $bindStock, $idStock);

            /* then update next level object (warehouse) */
            $tbl = EntityWarehouse::ENTITY_NAME;
            $bindWrhs = [
                EntityWarehouse::ATTR_CODE => $data->getCode(),
                EntityWarehouse::ATTR_CURRENCY => $data->getCurrency(),
                EntityWarehouse::ATTR_COUNTRY_CODE => $data->getCountryCode(),
                EntityWarehouse::ATTR_NOTE => $data->getNote()
            ];
            $idWrhs = [EntityWarehouse::ATTR_STOCK_REF => $id];
            $this->_repoGeneric->updateEntityById($tbl, $bindWrhs, $idWrhs);
            /* commit changes and compose result data object */
            $this->_manTrans->commit($def);
        } finally {
            $this->_manTrans->end($def);
        }
    }


}