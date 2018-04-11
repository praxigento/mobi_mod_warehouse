<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Warehouse\Plugin\Quote\Model;

use Praxigento\Warehouse\Config as Cfg;
use Praxigento\Warehouse\Repo\Data\Quote as EWrhsQuote;

class QuoteRepository
{
    private static $counter = 0;
    /** @var \Praxigento\Core\App\Repo\IGeneric */
    private $daoGeneric;
    /** @var \Praxigento\Warehouse\Repo\Dao\Quote */
    private $daoWrhsQuote;
    /** @var \Praxigento\Warehouse\Api\Helper\Stock */
    private $hlpStock;
    /** @var \Magento\Framework\Session\SessionManager */
    private $sessionManager;
    /** @var \Magento\Store\Model\StoreManagerInterface */
    private $storeManager;
    /** @var \Magento\Customer\Model\Session */
    private $customerSession;

    public function __construct(
        \Magento\Framework\Session\SessionManager $sessionManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Praxigento\Core\App\Repo\IGeneric $daoGeneric,
        \Praxigento\Warehouse\Repo\Dao\Quote $daoWrhsQuote,
        \Praxigento\Warehouse\Api\Helper\Stock $hlpStock
    ) {
        $this->sessionManager = $sessionManager;
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
        $this->daoGeneric = $daoGeneric;
        $this->daoWrhsQuote = $daoWrhsQuote;
        $this->hlpStock = $hlpStock;
    }

    /**
     * Synchronize current store_id with quote.store_id and change quote currency if required
     * (store switching does not change store_id in quote).
     *
     * @param \Magento\Quote\Model\QuoteRepository $subject
     * @param \Magento\Quote\Api\Data\CartInterface $result
     * @return \Magento\Quote\Api\Data\CartInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function aroundGetActive(
        \Magento\Quote\Model\QuoteRepository $subject,
        \Closure $proceed,
        $quoteId
    ) {
        self::$counter++;
        if (self::$counter <= 1) {
            $found = $this->daoWrhsQuote->getById($quoteId);
            if (!$found) {
                /* this quote is not registered yet */
                /** @var \Magento\Quote\Api\Data\CartInterface $result */
                $result = $proceed($quoteId);
                $custId = $result->getCustomerId();
                $storeId = $result->getStoreId();
                $stockId = $this->hlpStock->getStockIdByStoreId($storeId);
                $this->registerQuote($quoteId, $custId, $stockId);
            } else {
                /* this quote is already registered */
                $custId = $found->getCustRef();
                $stockId = $found->getStockRef();
                /* we need to check original stock ID */
                $stockIdCurrent = $this->getCurrentStockId();
                $custIdCurrent = $this->customerSession->getCustomerId();
                if (
                    ($stockId != $stockIdCurrent) ||
                    ($custId != $custIdCurrent)
                ) {
                    /* there is original stock ID in warehouse registry and it is not current stock */
                    /* this exception will be thrown in \Magento\Checkout\Model\Session::getQuote */
                    /* ... and new quote will be created  */
                    throw new \Magento\Framework\Exception\NoSuchEntityException();
                }
                $result = $proceed($quoteId);
            }
        } else {
            $result = $proceed($quoteId);
        }
        return $result;
    }

    public function aroundGetActiveForCustomer(
        \Magento\Quote\Model\QuoteRepository $subject,
        \Closure $proceed,
        $customerId,
        array $sharedStoreIds = []
    ) {
        /* find registered quote by current stock & customer ID */
        $stockId = $this->getCurrentStockId();
        $found = $this->getQuoteIdByCustomerAndStock($customerId, $stockId);
        /* set all quotes inactive */
        $this->setQuotesAllInactive($customerId);
        if ($found) {
            /* set found quote as active */
            $this->setQuoteActive($found);
        }
        /* ... then load active quote */
        $result = $proceed($customerId, $sharedStoreIds);
        return $result;
    }

    private function getCurrentStockId()
    {
        $store = $this->storeManager->getStore();
        $storeId = $store->getId();
        $result = $this->hlpStock->getStockIdByStoreId($storeId);
        return $result;
    }

    private function getQuoteIdByCustomerAndStock($custId, $stockId)
    {
        $result = 0;
        $byCust = EWrhsQuote::A_CUST_REF . '=' . (int)$custId;
        $byStock = EWrhsQuote::A_STOCK_REF . '=' . (int)$stockId;
        $where = "($byCust) AND ($byStock)";
        $rs = $this->daoWrhsQuote->get($where);
        if (is_array($rs) && count($rs) == 1) {
            $one = reset($rs);
            $result = $one->getQuoteRef();
        }
        return $result;
    }

    private function registerQuote($quoteId, $custId, $stockId)
    {
        $entity = new EWrhsQuote();
        $entity->setCustRef($custId);
        $entity->setQuoteRef($quoteId);
        $entity->setStockRef($stockId);
        $this->daoWrhsQuote->create($entity);
    }

    private function setQuoteActive($quoteId)
    {
        $bind = [
            Cfg::E_QUOTE_A_IS_ACTIVE => true
        ];
        $where = Cfg::E_QUOTE_A_ENTITY_ID . '=' . (int)$quoteId;
        $this->daoGeneric->updateEntity(Cfg::ENTITY_MAGE_QUOTE, $bind, $where);
    }

    /**
     * Set all customer quotes as inactive.
     *
     * @param int $custId
     */
    private function setQuotesAllInactive($custId)
    {
        $bind = [
            Cfg::E_QUOTE_A_IS_ACTIVE => false
        ];
        $where = Cfg::E_QUOTE_A_CUSTOMER_ID . '=' . (int)$custId;
        $this->daoGeneric->updateEntity(Cfg::ENTITY_MAGE_QUOTE, $bind, $where);
    }
}