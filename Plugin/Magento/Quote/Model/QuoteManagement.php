<?php
/**
 * Register new quote on sale order reorder by customer.
 *
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2021
 */

namespace Praxigento\Warehouse\Plugin\Magento\Quote\Model;

use Praxigento\Warehouse\Repo\Data\Quote as EWrhsQuote;

class QuoteManagement {
    /** @var \Praxigento\Warehouse\Repo\Dao\Quote */
    private $daoWrhsQuote;
    /** @var \Praxigento\Warehouse\Api\Helper\Stock */
    private $hlpStock;
    /** @var \Magento\Store\Model\StoreManagerInterface */
    private $storeManager;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Praxigento\Warehouse\Api\Helper\Stock $hlpStock,
        \Praxigento\Warehouse\Repo\Dao\Quote $daoWrhsQuote
    ) {
        $this->storeManager = $storeManager;
        $this->hlpStock = $hlpStock;
        $this->daoWrhsQuote = $daoWrhsQuote;
    }

    public function aroundCreateEmptyCartForCustomer(
        \Magento\Quote\Model\QuoteManagement $subject,
        \Closure $proceed,
        $customerId
    ) {
        $quoteId = $proceed($customerId);
        if ($quoteId) {
            $stockId = $this->getCurrentStockId();
            $this->registerQuote($quoteId, $customerId, $stockId);
        }
        return $quoteId;
    }

    private function getCurrentStockId() {
        $store = $this->storeManager->getStore();
        $storeId = $store->getId();
        $result = $this->hlpStock->getStockIdByStoreId($storeId);
        return $result;
    }

    /**
     * Get quote from registry by unique key.
     * @param int $custId
     * @param int $stockId
     * @return \Praxigento\Warehouse\Repo\Data\Quote|null
     */
    private function getRegisteredQuoteByUniqueKey($custId, $stockId) {
        $result = null;
        $byCustId = EWrhsQuote::A_CUST_REF . '=' . (int)$custId;
        $byStockId = EWrhsQuote::A_STOCK_REF . '=' . (int)$stockId;
        $where = "($byCustId) AND ($byStockId)";
        $rs = $this->daoWrhsQuote->get($where);
        if (count($rs) == 1) {
            $result = reset($rs);
        }
        return $result;
    }

    private function registerQuote($quoteId, $custId, $stockId) {
        $entity = new EWrhsQuote();
        $entity->setCustRef($custId);
        $entity->setQuoteRef($quoteId);
        $entity->setStockRef($stockId);
        /* validate existence of the other quote with the same $custId/$stockId */
        $found = $this->getRegisteredQuoteByUniqueKey($custId, $stockId);
        if ($found) {
            /* SAN-490: delete existing quote before creating */
            $existQuoteId = $found->getQuoteRef();
            $this->daoWrhsQuote->deleteById($existQuoteId);
        }
        $this->daoWrhsQuote->create($entity);
    }
}
