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
    /** alias for joined 'cataloginventory_stock_status'. */
    const AS_STOCK_INDEX = 'stock_index';
    /** @var \Praxigento\Warehouse\Api\Helper\Stock */
    protected $_manStock;

    public function __construct(
        \Praxigento\Warehouse\Api\Helper\Stock $manStock
    ) {
        $this->_manStock = $manStock;
    } // see \Magento\CatalogSearch\Model\Search\IndexBuilder::build

    /**
     * @param \Magento\CatalogSearch\Model\Search\IndexBuilder $subject
     * @param \Closure $proceed
     * @param \Magento\Framework\Search\RequestInterface $request
     * @return \Magento\Framework\DB\Select
     */
    public function aroundBuild(
        \Magento\CatalogSearch\Model\Search\IndexBuilder $subject,
        \Closure $proceed,
        \Magento\Framework\Search\RequestInterface $request
    ) {
        /** @var \Magento\Framework\DB\Select $result */
        $result = $proceed($request);
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
        return $result;
    }
}