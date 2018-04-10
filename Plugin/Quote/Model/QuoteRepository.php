<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Warehouse\Plugin\Quote\Model;

use Praxigento\Warehouse\Config as Cfg;

class QuoteRepository
{
    private static $counter = 0;
    /** @var \Praxigento\Core\App\Repo\IGeneric */
    private $daoGeneric;
    /** @var \Praxigento\Warehouse\Api\Helper\Stock */
    private $hlpStock;
    /** @var \Magento\Store\Model\StoreManagerInterface */
    private $storeManager;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Praxigento\Core\App\Repo\IGeneric $daoGeneric,
        \Praxigento\Warehouse\Api\Helper\Stock $hlpStock
    ) {
        $this->storeManager = $storeManager;
        $this->daoGeneric = $daoGeneric;
        $this->hlpStock = $hlpStock;
    }


    /**
     * Synchronize current store_id with quote.store_id and change quote currency if required
     * (store switching does not change store_id in quote).
     *
     * @param \Magento\Quote\Api\CartRepositoryInterface $subject
     * @param $result
     * @return mixed
     */
    public function afterGetActive(
        \Magento\Quote\Api\CartRepositoryInterface $subject,
        \Magento\Quote\Api\Data\CartInterface $result
    ) {
        self::$counter++;
        /* get store ID for quote from DB */
        $quoteId = $result->getId();
        $storeIdQuote = $this->getStoreIdByQuoteId($quoteId);
        /* get current store ID */
        $store = $this->storeManager->getStore();
        $storeIdCurrent = $store->getId();

        $currBase = $result->getBaseCurrencyCode();
        $quoteCurr = $result->getQuoteCurrencyCode();
        $storeCurr = $result->getStoreCurrencyCode();

        /* we call to getActive() on total recalc. */
        if (
            ($storeIdCurrent != $storeIdQuote) &&
            (self::$counter >= 1)
        ) {
            $result->getQuoteCurrencyCode(null);
            $this->saveNewStoreId($quoteId, $storeIdCurrent);
        }
        return $result;
    }

    /**
     * We should directly load storeId for quote, because it is forced
     * in \Magento\Quote\Model\QuoteRepository::loadQuote
     *
     * @param int $quoteId
     * @return int
     */
    private function getStoreIdByQuoteId($quoteId)
    {
        $result = 0;
        $pk = [
            Cfg::E_QUOTE_A_ENTITY_ID => $quoteId
        ];
        $entity = $this->daoGeneric->getEntityByPk(Cfg::ENTITY_MAGE_QUOTE, $pk);
        if ($entity) {
            $result = $entity[Cfg::E_QUOTE_A_STORE_ID];
        }
        return $result;
    }

    private function saveNewStoreId($quoteId, $storeId)
    {
        /* update store_id in quote */
        $bind = [
            Cfg::E_QUOTE_A_STORE_ID => $storeId
        ];
        $pk = [
            Cfg::E_QUOTE_A_ENTITY_ID => $quoteId
        ];
        $updated = $this->daoGeneric->updateEntityById(Cfg::ENTITY_MAGE_QUOTE, $bind, $pk);
        if ($updated) {
            /* update store_id in quote items */
            $bind = [
                Cfg::E_QUOTE_ITEM_A_STORE_ID => $storeId
            ];
            $where = Cfg::E_QUOTE_ITEM_A_QUOTE_ID . '=' . (int)$quoteId;
            $this->daoGeneric->updateEntity(Cfg::ENTITY_MAGE_QUOTE_ITEM, $bind, $where);
        }
    }
}