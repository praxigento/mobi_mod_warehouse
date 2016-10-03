<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Repo\Agg\Def\Warehouse;

use Praxigento\Warehouse\Config as Cfg;
use Praxigento\Warehouse\Data\Agg\Warehouse as AggWarehouse;
use Praxigento\Warehouse\Data\Entity\Warehouse as EntityWarehouse;
use Praxigento\Warehouse\Repo\Agg\IWarehouse as IRepoWarehouse;

class SelectFactory
    extends \Praxigento\Core\Repo\Agg\BaseSelectFactory
{

    public function getQueryToSelectCount()
    {
        $result = $this->_conn->select();
        /* aliases and tables */
        $asStock = IRepoWarehouse::AS_STOCK;
        $asWrhs = IRepoWarehouse::AS_WRHS;
        $tblStock = [$asStock => $this->_resource->getTableName(Cfg::ENTITY_MAGE_CATALOGINVENTORY_STOCK)];
        $tblWrhs = [$asWrhs => $this->_resource->getTableName(EntityWarehouse::ENTITY_NAME)];
        /* SELECT FROM cataloginventory_stock */
        $cols = "COUNT(" . Cfg::E_CATINV_STOCK_A_STOCK_ID . ")";
        $result->from($tblStock, $cols);
        /* LEFT JOIN prxgt_wrhs_wrhs */
        $cond = $asWrhs . '.' . EntityWarehouse::ATTR_STOCK_REF . '=' . $asStock . '.' . Cfg::E_CATINV_STOCK_A_STOCK_ID;
        $cols = [];
        $result->joinLeft($tblWrhs, $cond, $cols);
        return $result;
    }

    public function getQueryToSelect()
    {
        $result = $this->_conn->select();
        /* aliases and tables */
        $asStock = IRepoWarehouse::AS_STOCK;
        $asWrhs = IRepoWarehouse::AS_WRHS;
        $tblStock = [$asStock => $this->_resource->getTableName(Cfg::ENTITY_MAGE_CATALOGINVENTORY_STOCK)];
        $tblWrhs = [$asWrhs => $this->_resource->getTableName(EntityWarehouse::ENTITY_NAME)];
        /* SELECT FROM cataloginventory_stock */
        $cols = [
            AggWarehouse::AS_ID => Cfg::E_CATINV_STOCK_A_STOCK_ID,
            AggWarehouse::AS_CODE => Cfg::E_CATINV_STOCK_A_STOCK_NAME,
            AggWarehouse::AS_WEBSITE_ID => Cfg::E_CATINV_STOCK_A_WEBSITE_ID
        ];
        $result->from($tblStock, $cols);
        /* LEFT JOIN prxgt_wrhs_wrhs */
        $cond = $asWrhs . '.' . EntityWarehouse::ATTR_STOCK_REF . '=' . $asStock . '.' . Cfg::E_CATINV_STOCK_A_STOCK_ID;
        $cols = [
            AggWarehouse::AS_CURRENCY => EntityWarehouse::ATTR_CURRENCY,
            AggWarehouse::AS_COUNTRY_CODE => EntityWarehouse::ATTR_COUNTRY_CODE,
            AggWarehouse::AS_NOTE => EntityWarehouse::ATTR_NOTE
        ];
        $result->joinLeft($tblWrhs, $cond, $cols);
        return $result;
    }
}