<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Plugin\Store\Model;


/**
 * Replace Magento's base currency code by warehouse currency code.
 */
class Store
{
    /** @var \Praxigento\Warehouse\Tool\IStockManager */
    protected $hlpStock;
    /** @var \Praxigento\Warehouse\Repo\Entity\Def\Warehouse */
    protected $repoWrhs;

    public function __construct(
        \Praxigento\Warehouse\Tool\IStockManager $hlpStock,
        \Praxigento\Warehouse\Repo\Entity\Def\Warehouse $repoWrhs
    ) {
        $this->hlpStock = $hlpStock;
        $this->repoWrhs = $repoWrhs;
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
        $wrhsDo = $this->repoWrhs->getById($stockId);
        $curCode = $wrhsDo->getCurrency();
        $result = $curCode;
        return $result;
    }

}