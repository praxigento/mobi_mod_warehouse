<?php
/**
 * Repository to operate with 'Warehouse" aggregate in this module.
 *
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Repo\Agg\Def;

use Magento\Framework\ObjectManagerInterface;
use Praxigento\Core\Repo\IBasic as IBasicRepo;
use Praxigento\Warehouse\Config as Cfg;
use Praxigento\Warehouse\Data\Agg\Warehouse as AggWarehouse;
use Praxigento\Warehouse\Data\Entity\Warehouse as EntityWarehouse;
use Praxigento\Warehouse\Repo\Agg\IWarehouse;

class Warehouse implements IWarehouse
{
    const AS_STOCK = 'cs';
    const AS_WRHS = 'pww';
    /** @var  \Magento\Framework\DB\Adapter\AdapterInterface */
    protected $_conn;
    /** @var  ObjectManagerInterface */
    protected $_manObj;
    /** @var  \Praxigento\Core\Repo\ITransactionManager */
    protected $_manTrans;
    /** @var IBasicRepo */
    protected $_repoBasic;
    /** @var \Magento\Framework\App\ResourceConnection */
    protected $_resource;

    public function __construct(
        ObjectManagerInterface $manObj,
        \Praxigento\Core\Repo\ITransactionManager $manTrans,
        \Magento\Framework\App\ResourceConnection $resource,
        IBasicRepo $repoBasic
    ) {
        $this->_manObj = $manObj;
        $this->_manTrans = $manTrans;
        $this->_resource = $resource;
        $this->_conn = $resource->getConnection();
        $this->_repoBasic = $repoBasic;
    }

    /**
     * Create JOIN to get aggregated data.
     *
     * @return \Magento\Framework\DB\Select
     */
    protected function _initQueryRead()
    {
        $result = $this->_conn->select();
        /* aliases and tables */
        $asStock = self::AS_STOCK;
        $asWrhs = self::AS_WRHS;
        $tblStock = [$asStock => $this->_conn->getTableName(Cfg::ENTITY_MAGE_CATALOGINVENTORY_STOCK)];
        $tblWrhs = [$asWrhs => $this->_conn->getTableName(EntityWarehouse::ENTITY_NAME)];
        /* SELECT FROM cataloginventory_stock */
        $cols = [
            AggWarehouse::AS_ID => Cfg::E_CATINV_STOCK_A_STOCK_ID,
            AggWarehouse::AS_CODE => Cfg::E_CATINV_STOCK_A_STOCK_NAME,
            AggWarehouse::AS_WEBSITE_ID => Cfg::E_CATINV_STOCK_A_WEBSITE_ID
        ];
        $result->from($tblStock, $cols);
        /* LEFT JOIN prxgt_wrhs_wrhs */
        $on = $asWrhs . '.' . EntityWarehouse::ATTR_STOCK_REF . '=' . $asStock . '.' . Cfg::E_CATINV_STOCK_A_STOCK_ID;
        $cols = [
            AggWarehouse::AS_CURRENCY => EntityWarehouse::ATTR_CURRENCY,
            AggWarehouse::AS_NOTE => EntityWarehouse::ATTR_NOTE
        ];
        $result->joinLeft($tblWrhs, $on, $cols);
        return $result;
    }

    protected function _initResultRead($data)
    {
        /** @var  $result AggWarehouse */
        $result = $this->_manObj->create(AggWarehouse::class);
        $result->setData($data);
        return $result;
    }

    public function create($data)
    {
        $result = $data;
        $trans = $this->_manTrans->transactionBegin();
        try {
            /* create top level object (catalog inventory stock) */
            $tbl = Cfg::ENTITY_MAGE_CATALOGINVENTORY_STOCK;
            $bind = [
                Cfg::E_CATINV_STOCK_A_WEBSITE_ID => $data->getWebsiteId(),
                Cfg::E_CATINV_STOCK_A_STOCK_NAME => $data->getCode()
            ];
            $id = $this->_repoBasic->addEntity($tbl, $bind);
            $result->setId($id);
            /* then create next level object (warehouse) */
            $tbl = EntityWarehouse::ENTITY_NAME;
            $bind = [
                EntityWarehouse::ATTR_STOCK_REF => $id,
                EntityWarehouse::ATTR_CODE => $data->getCode(),
                EntityWarehouse::ATTR_CURRENCY => $data->getCurrency(),
                EntityWarehouse::ATTR_NOTE => $data->getNote()
            ];
            $this->_repoBasic->addEntity($tbl, $bind);
            /* commit changes */
            $this->_manTrans->transactionCommit($trans);
        } finally {
            $this->_manTrans->transactionClose($trans);
        }
        return $result;
    }

    public function getById($id)
    {
        /** @var  $result AggWarehouse */
        $result = null;
        $query = $this->_initQueryRead();
        $query->where(self::AS_STOCK . '.' . Cfg::E_CATINV_STOCK_A_STOCK_ID . '=:id');
        $data = $this->_conn->fetchRow($query, ['id' => $id]);
        if ($data) {
            $result = $this->_initResultRead($data);
        }
        return $result;
    }
}