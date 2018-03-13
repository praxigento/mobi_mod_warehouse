<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Helper;

use Praxigento\Warehouse\Repo\Query\Product\GetPrices as QBGetPrices;

/**
 * Helper for cacheable loading of the product prices.
 */
class PriceLoader
{
    /** @var array [$prodId][$stockId][$groupId] => [$wrhsPrice, $wrhsGroupPrice] */
    private $cache = [];

    /** @var \Praxigento\Warehouse\Repo\Query\Product\GetPrices */
    private $qbGetPrices;

    public function __construct(
        \Praxigento\Warehouse\Repo\Query\Product\GetPrices $qbGetPrices
    ) {
        $this->qbGetPrices = $qbGetPrices;
    }

    /**
     * @param int $prodId
     * @param int $stockId
     * @param int $groupId
     * @return array [wrhsPrice, wrhsGroupPrice]
     */
    public function load($prodId, $stockId, $groupId)
    {
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