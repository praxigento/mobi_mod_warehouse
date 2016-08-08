<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Plugin\CatalogSearch\Model\Search;

use Praxigento\Warehouse\Config as Cfg;

/**
 * Add stock information to catalog search query (MOBI-340).
 */
class IndexBuilder
{
    /** @var \Praxigento\Warehouse\Tool\IStockManager */
    protected $_manStock;

    public function __construct(
        \Praxigento\Warehouse\Tool\IStockManager $manStock
    ) {
        $this->_manStock = $manStock;
    }

    /** alias for joined 'cataloginventory_stock_status'. */
    const AS_STOCK_INDEX = 'stock_index'; // see \Magento\CatalogSearch\Model\Search\IndexBuilder::build

    public function aroundBuild(
        \Magento\CatalogSearch\Model\Search\IndexBuilder $subject,
        \Closure $proceed,
        \Magento\Framework\Search\RequestInterface $request
    ) {
        /** @var \Magento\Framework\DB\Select $result */
        $result = $proceed($request);
//        $storeId = $result->getStoreId();
//        $stockId = $this->_manStock->getStockIdByStoreId($storeId);
        $from = $result->getPart(\Magento\Framework\DB\Select::FROM);
        if (isset($from[self::AS_STOCK_INDEX])) {
            $dimensions = $request->getDimensions();
            /** @var \Magento\Framework\Search\Request\Dimension $dimension */
            $dimension = reset($dimensions);
            $storeId = $dimension->getValue();
            $stockId = (int)$this->_manStock->getStockIdByStoreId($storeId);
            $byStockId = self::AS_STOCK_INDEX . '.' . Cfg::E_CATINV_STOCK_STATUS_A_STOCK_ID . '=' . $stockId;
            $result->where($byStockId);
        }
        /** @var \Magento\Framework\DB\Select $sql */
//        $sql = $result->getSelectSql();
        /** @var \Magento\Framework\DB\Select $sqlCount */
//        $sqlCount = $result->getSelectCountSql();
        $select = (string)$result;
        return $result;
    }
}