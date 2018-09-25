<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Plugin\Magento\Store\Model;


/**
 * Replace Magento's base currency code by warehouse currency code.
 */
class Store
{
    /** @var \Praxigento\Warehouse\Api\Helper\Stock */
    private $hlpStock;
    /** @var \Praxigento\Warehouse\Repo\Dao\Warehouse */
    private $daoWrhs;

    public function __construct(
        \Praxigento\Warehouse\Api\Helper\Stock $hlpStock,
        \Praxigento\Warehouse\Repo\Dao\Warehouse $daoWrhs
    ) {
        $this->hlpStock = $hlpStock;
        $this->daoWrhs = $daoWrhs;
    }

    /**
     * Replace Magento's base currency code by warehouse currency code.
     *
     * @param \Magento\Store\Model\Store $subject
     * @param \Closure $proceed
     * @return string
     */
    public function aroundGetBaseCurrencyCode(
        \Magento\Store\Model\Store $subject,
        \Closure $proceed
    ) {
        /* call parent method to proceed functionality */
        $proceed();
        /* then replace store currency code by warehouse currency code */
        $storeId = $subject->getId();
        $stockId = $this->hlpStock->getStockIdByStoreId($storeId);
        $wrhsDo = $this->daoWrhs->getById($stockId);
        $curCode = $wrhsDo->getCurrency();
        $result = $curCode;
        return $result;
    }

}