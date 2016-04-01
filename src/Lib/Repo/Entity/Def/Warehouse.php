<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Lib\Repo\Entity\Def;

use Praxigento\Core\Lib\Context as Ctx;
use Praxigento\Core\Lib\Context\IObjectManager;
use Praxigento\Core\Lib\Context\ObjectManagerFactory;
use Praxigento\Core\Repo\IBasic as IBasicRepo;
use Praxigento\Warehouse\Config as Cfg;
use Praxigento\Warehouse\Lib\Data\Agg\Warehouse as AggWarehouse;
use Praxigento\Warehouse\Lib\Data\Entity\Warehouse as EntityWarehouse;
use Praxigento\Warehouse\Lib\Repo\Entity\IWarehouse;

class Warehouse implements IWarehouse
{
    const AS_STOCK = 'cs';
    const AS_WRHS = 'pww';
    /** @var IBasicRepo */
    protected $_repoBasic;
    /** @var  IObjectManager */
    protected $_manObj;

    public function __construct(
        ObjectManagerFactory $factObjMan,
        IBasicRepo $repoBasic
    ) {
        $this->_manObj = $factObjMan->create();
        $this->_repoBasic = $repoBasic;
    }

    protected function _initQueryRead()
    {
        $dba = $this->_repoBasic->getDba();
        $conn = $dba->getDefaultConnection();
        $result = $conn->select();
        /* aliases and tables */
        $asStock = self::AS_STOCK;
        $asWrhs = self::AS_WRHS;
        $tblStock = [$asStock => $dba->getTableName(Cfg::ENTITY_MAGE_CATALOGINVENTORY_STOCK)];
        $tblWrhs = [$asWrhs => $dba->getTableName(EntityWarehouse::ENTITY_NAME)];
        /* SELECT FROM cataloginventory_stock */
        $cols = [
            AggWarehouse::AS_ID => Cfg::E_CATINV_STOCK_A_STOCK_ID,
            AggWarehouse::AS_CODE => Cfg::E_CATINV_STOCK_A_STOCK_NAME,
            AggWarehouse::AS_WEBSITE_ID => Cfg::E_CATINV_STOCK_A_WEBSITE_ID
        ];
        $result->from($tblStock, $cols);
        /* LEFT JOIN prxgt_wrhs_warehouse */
        $on = $asWrhs . '.' . EntityWarehouse::ATTR_STOCK_REF . '=' . $asStock . '.' . Cfg::E_CATINV_STOCK_A_STOCK_ID;
        $cols = [
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
        $dba = $this->_repoBasic->getDba();
        $manTrans = $dba->getTransactionManager();
        $trans = $manTrans->transactionBegin();
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
                EntityWarehouse::ATTR_NOTE => $data->getNote()
            ];
            $this->_repoBasic->addEntity($tbl, $bind);
            /* commit changes */
            $manTrans->transactionCommit($trans);
        } finally {
            $manTrans->transactionClose($trans);
        }
        return $result;
    }

    public function getById($id)
    {
        /** @var  $result AggWarehouse */
        $result = null;
        $query = $this->_initQueryRead();
        $query->where(self::AS_STOCK . '.' . Cfg::E_CATINV_STOCK_A_STOCK_ID . '=:id');
        $sql = (string)$query;
        $dba = $this->_repoBasic->getDba();
        $conn = $dba->getDefaultConnection();
        $data = $conn->fetchRow($query, ['id' => $id]);
        if ($data) {
            $result = $this->_initResultRead($data);
        }
        return $result;
    }
}