<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Warehouse\Plugin\Magento\Quote\Model;

use Praxigento\Warehouse\Config as Cfg;
use Praxigento\Warehouse\Repo\Data\Quote as EWrhsQuote;

class QuoteRepository
{
    const SESS_QUOTE_REGISTRY = 'prxgt_quote_registry';

    private static $counter = 0;
    /** @var \Praxigento\Core\Api\App\Repo\Generic */
    private $daoGeneric;
    /** @var \Praxigento\Warehouse\Repo\Dao\Quote */
    private $daoWrhsQuote;
    /** @var \Praxigento\Warehouse\Api\Helper\Stock */
    private $hlpStock;
    /** @var \Magento\Checkout\Model\Session */
    private $sessCheckout;
    /** @var \Magento\Customer\Model\Session */
    private $sessCustomer;
    /** @var \Magento\Store\Model\StoreManagerInterface */
    private $storeManager;

    public function __construct(
        \Magento\Checkout\Model\Session $sessCheckout,
        \Magento\Customer\Model\Session $sessCustomer,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Praxigento\Core\Api\App\Repo\Generic $daoGeneric,
        \Praxigento\Warehouse\Repo\Dao\Quote $daoWrhsQuote,
        \Praxigento\Warehouse\Api\Helper\Stock $hlpStock
    ) {
        $this->sessCheckout = $sessCheckout;
        $this->sessCustomer = $sessCustomer;
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
        $quoteId,
        array $sharedStoreIds = []
    ) {
        self::$counter++;
        if (self::$counter <= 1) {
            $found = $this->findQuoteById($quoteId);
            if (!$found) {
                /* this quote is not registered yet */
                /** @var \Magento\Quote\Api\Data\CartInterface $result */
                $result = $proceed($quoteId, $sharedStoreIds);
                $custId = $result->getCustomerId();
                $storeId = $result->getStoreId();
                $stockId = $this->hlpStock->getStockIdByStoreId($storeId);
                $this->registerQuote($quoteId, $custId, $stockId);
            } else {
                /* this quote is already registered */
                $custId = $found->getCustRef();
                if (is_null($custId)) {
                    /* anonymous customer, try to find quote ID by stock ID*/
                    $stockId = $this->getCurrentStockId();
                    $fromSession = $this->findQuoteByStockIdInSession($stockId);
                    if ($fromSession) {
                        $quoteId = $fromSession->getQuoteRef();
                        $this->sessCheckout->setQuoteId($quoteId);
                        $result = $proceed($quoteId, $sharedStoreIds);
                    } else {
                        /* new empty session */
                        /* this exception will be caught in \Magento\Checkout\Model\Session::getQuote */
                        /* ... and new quote will be created  */
                        $this->sessCheckout->setQuoteId(null);
                        throw new \Magento\Framework\Exception\NoSuchEntityException();
                    }
                } else {
                    /* authenticated customer, check current stock */
                    $stockId = $found->getStockRef();
                    /* we need to check original stock ID */
                    $stockIdCurrent = $this->getCurrentStockId();
                    $custIdCurrent = $this->sessCustomer->getCustomerId();
                    if (
                        ($stockId != $stockIdCurrent) ||
                        ($custId != $custIdCurrent)
                    ) {
                        /* there is original stock ID in warehouse registry and it is not current stock */
                        /* this exception will be caught in \Magento\Checkout\Model\Session::getQuote */
                        /* ... and new quote will be created  */
                        $this->sessCheckout->setQuoteId(null);
                        throw new \Magento\Framework\Exception\NoSuchEntityException();
                    }
                    $result = $proceed($quoteId, $sharedStoreIds);
                }

            }
        } else {
            $result = $proceed($quoteId, $sharedStoreIds);
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
        /** @var \Magento\Quote\Model\Quote $result */
        $result = $proceed($customerId, $sharedStoreIds);
        /* merge anonymous session if exists */
        /* TODO: this code is doubt; session should be reset after authentication */
        $fromSession = $this->findQuoteByStockIdInSession($stockId);
        if ($fromSession) {
            $result->merge($fromSession);
            $quoteIdSess = $fromSession->getQuoteRef();
            $this->unsetQuoteInSession($quoteIdSess);
        }
        return $result;
    }

    /**
     * Find quote registry data in DB (authenticated customers) or session (anonymous).
     *
     * @param $quoteId
     * @return bool|\Praxigento\Warehouse\Repo\Data\Quote
     * @throws \Exception
     */
    private function findQuoteById($quoteId)
    {
        $result = false;
        if ($this->isCustomerAuthenticated()) {
            $result = $this->daoWrhsQuote->getById($quoteId);
        } else {
            $sessionReg = $this->sessCustomer->getData(self::SESS_QUOTE_REGISTRY);
            if (
                $sessionReg && isset($sessionReg[$quoteId])
            ) {
                $result = $sessionReg[$quoteId];
            }
        }
        return $result;
    }

    /**
     * Find quote registry data by stock ID in session (for anonymous customers).
     *
     * @param $stockId
     * @return bool|\Praxigento\Warehouse\Repo\Data\Quote
     */
    private function findQuoteByStockIdInSession($stockId)
    {
        $result = false;
        $sessionReg = $this->sessCustomer->getData(self::SESS_QUOTE_REGISTRY);
        if (is_array($sessionReg)) {
            /** @var EWrhsQuote $one */
            foreach ($sessionReg as $one) {
                if ($one->getStockRef() == $stockId) {
                    $result = $one;
                    break;
                }
            }
        }
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

    /** @return bool */
    private function isCustomerAuthenticated()
    {
        $result = $this->sessCustomer->isLoggedIn();
        return $result;
    }

    private function registerQuote($quoteId, $custId, $stockId)
    {
        $entity = new EWrhsQuote();
        $entity->setCustRef($custId);
        $entity->setQuoteRef($quoteId);
        $entity->setStockRef($stockId);
        if ($this->isCustomerAuthenticated()) {
            $this->daoWrhsQuote->create($entity);
        } else {
            $quoteReg = $this->sessCustomer->getData(self::SESS_QUOTE_REGISTRY);
            if (!is_array($quoteReg)) $quoteReg = [];
            $quoteReg[$quoteId] = $entity;
            $this->sessCustomer->setData(self::SESS_QUOTE_REGISTRY, $quoteReg);
        }
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

    private function unsetQuoteInSession($quoteId)
    {
        $registry = $this->sessCustomer->getData(self::SESS_QUOTE_REGISTRY);
        if (is_array($registry) && isset($registry[$quoteId])) {
            unset($registry[$quoteId]);
            $this->sessCustomer->setData(self::SESS_QUOTE_REGISTRY, $registry);
        }

    }
}