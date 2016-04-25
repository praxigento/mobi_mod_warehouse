<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Repo\Agg\Def\Warehouse;


use Magento\Framework\App\ResourceConnection;
use Praxigento\Core\Repo\IHasSelectQuery;
use Praxigento\Warehouse\Config as Cfg;
use Praxigento\Warehouse\Data\Agg\Warehouse as AggWarehouse;
use Praxigento\Warehouse\Data\Entity\Warehouse as EntityWarehouse;
use Praxigento\Warehouse\Repo\Agg\IWarehouse as IRepoWarehouse;

class SelectFactory implements IHasSelectQuery
{
    /** @var  \Magento\Framework\DB\Adapter\AdapterInterface */
    protected $_conn;
    /** @var \Magento\Framework\App\ResourceConnection */
    protected $_resource;

    public function __construct(
        ResourceConnection $resource
    ) {
        $this->_resource = $resource;
        $this->_conn = $resource->getConnection();
    }

    public function getSelectQuery()
    {
        $result = $this->_conn->select();
        /* aliases and tables */
        $asStock = IRepoWarehouse::AS_STOCK;
        $asWrhs = IRepoWarehouse::AS_WRHS;
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
}