<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Plugin\Tax\Model;

/**
 * Replace website scoped Shipping Origin (Configuration / Sales / Shipping Sales / Origin)
 * by warehouse data.
 *
 * Plugin for \Magento\Tax\Model\Calculation.
 *
 */
class Calculation
{
    /** @var \Praxigento\Warehouse\Api\Helper\Stock */
    protected $_manStock;
    /** @var \Praxigento\Warehouse\Repo\Dao\Warehouse */
    protected $_repoWrhs;

    public function __construct(
        \Praxigento\Warehouse\Api\Helper\Stock $manStock,
        \Praxigento\Warehouse\Repo\Dao\Warehouse $repoWrhs
    ) {
        $this->_manStock = $manStock;
        $this->_repoWrhs = $repoWrhs;
    }

    public function afterGetRateRequest(
        \Magento\Tax\Model\Calculation $subject,
        $result
    ) {
        $storeId = $result->getStore();
        $stockId = $this->_manStock->getStockIdByStoreId($storeId);
        $wrhs = $this->_repoWrhs->getById($stockId);
        if ($wrhs) {
            /* MOBI-341 : replace country code only */
            $countryCode = $wrhs->getCountryCode();
            $result->setCountryId($countryCode);
        }
        return $result;
    }
}