<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Helper;

use Praxigento\Warehouse\Repo\Query\Product\GetPrices as QBGetPrices;

/**
 * Helper for cacheable loading of the product prices (warehouse & wrhs group).
 */
class PriceLoader
{
    /** @var array [$prodId][$stockId][$groupId] => [$wrhsPrice, $wrhsGroupPrice] */
    private $cache = [];
    /** @var \Praxigento\Warehouse\Api\Helper\Stock */
    private $hlpStock;
    /** @var \Magento\Customer\Model\Session */
    private $session;
    /** @var \Praxigento\Warehouse\Repo\Query\Product\GetPrices */
    private $qbGetPrices;

    public function __construct(
        \Magento\Customer\Model\Session $session,
        \Praxigento\Warehouse\Repo\Query\Product\GetPrices $qbGetPrices,
        \Praxigento\Warehouse\Api\Helper\Stock $hlpStock
    ) {
        $this->session = $session;
        $this->qbGetPrices = $qbGetPrices;
        $this->hlpStock = $hlpStock;
    }

    /**
     * @param int $prodId
     * @param int $storeId
     * @param int $groupId
     * @return array [wrhsPrice, wrhsGroupPrice]
     */
    public function load($prodId, $storeId = null, $groupId = null)
    {
        /* define key parameters */
        if (is_null($storeId)) {
            $stockId = $this->hlpStock->getDefaultStockId();
        } else {
            $stockId = $this->hlpStock->getStockIdByStoreId($storeId);
        }
        if (is_null($groupId)) {
            $groupId = $this->session->getCustomerGroupId();
        }
        /* find price for key */
        if (!isset($this->cache[$prodId][$stockId][$groupId])) {
            $query = $this->qbGetPrices->build();
            $conn = $query->getConnection();
            $bind = [
                QBGetPrices::BND_PROD_ID => $prodId,
                QBGetPrices::BND_STOCK_ID => $stockId,
                QBGetPrices::BND_GROUP_ID => $groupId
            ];
            $rs = $conn->fetchRow($query, $bind);
            $wrhsPrice = $grpPrice = null;
            if ($rs) {
                $wrhsPrice = $rs[QBGetPrices::A_WRHS_PRICE];
                $grpPrice = $rs[QBGetPrices::A_WRHS_GROUP_PRICE];
            }
            $this->cache[$prodId][$stockId][$groupId] = [$wrhsPrice, $grpPrice];
        }
        return $this->cache[$prodId][$stockId][$groupId];
    }
}