<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Warehouse\Plugin\Quote\Model;

class QuoteRepository
{
    private static $counter = 0;
    /** @var \Praxigento\Warehouse\Repo\Dao\Quote */
    private $daoWrhsQuote;
    /** @var \Praxigento\Warehouse\Api\Helper\Stock */
    private $hlpStock;
    /** @var \Magento\Framework\Session\SessionManager */
    private $sessionManager;
    /** @var \Magento\Store\Model\StoreManagerInterface */
    private $storeManager;

    public function __construct(
        \Magento\Framework\Session\SessionManager $sessionManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Praxigento\Warehouse\Repo\Dao\Quote $daoWrhsQuote,
        \Praxigento\Warehouse\Api\Helper\Stock $hlpStock
    ) {
        $this->sessionManager = $sessionManager;
        $this->storeManager = $storeManager;
        $this->daoWrhsQuote = $daoWrhsQuote;
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
        if (self::$counter <= 1) {
            /* current store ID is set to quote on load */
            $quoteId = $result->getId();
            $storeId = $result->getStoreId();
            $stockId = $this->hlpStock->getStockIdByStoreId($storeId);
            /* we need to check original stock ID */
            $stockIdOrig = $this->getOriginalStoreId($quoteId);
            if (
                $stockIdOrig &&
                ($stockId != $stockIdOrig)
            ) {
                /* there is original stock ID in warehouse registry and it is not current stock */
                /* this exception will be thrown in \Magento\Checkout\Model\Session::getQuote */
                /* ... and new quote will be created  */
                throw new \Magento\Framework\Exception\NoSuchEntityException();
            }
        }
        return $result;
    }

    /**
     * @param int $quoteId
     * @return int
     */
    private function getOriginalStoreId($quoteId)
    {
        $result = 0;
        $found = $this->daoWrhsQuote->getById($quoteId);
        if ($found) {
            $result = $found->getStockRef();
        }
        return $result;
    }
}