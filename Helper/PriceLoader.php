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
    /** @var \Magento\Customer\Api\GroupManagementInterface */
    private $manGroup;
    /** @var \Praxigento\Warehouse\Repo\Query\Product\GetPrices */
    private $qbGetPrices;
    /** @var \Magento\Framework\Config\ScopeInterface */
    private $scope;
    /** @var \Magento\Customer\Model\Session */
    private $session;

    public function __construct(
        \Magento\Framework\Config\ScopeInterface $scope,
        \Magento\Customer\Model\Session $session,
        \Magento\Customer\Api\GroupManagementInterface $manGroup,
        \Praxigento\Warehouse\Repo\Query\Product\GetPrices $qbGetPrices,
        \Praxigento\Warehouse\Api\Helper\Stock $hlpStock
    ) {
        $this->scope = $scope;
        $this->session = $session;
        $this->manGroup = $manGroup;
        $this->qbGetPrices = $qbGetPrices;
        $this->hlpStock = $hlpStock;
    }

    /**
     * @param int $prodId
     * @param int $storeId
     * @param int $groupId
     * @return array [wrhsPrice, wrhsGroupPrice]
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function load($prodId, $storeId = null, $groupId = null)
    {
        /* define key parameters if missed (front & admin)*/
        $scope = $this->scope->getCurrentScope();
        /* stock ID */
        if (is_null($storeId)) {
            if ($scope != \Magento\Framework\App\Area::AREA_FRONTEND) {
                /* backend mode */
                $stockId = $this->hlpStock->getDefaultStockId();
            } else {
                /* frontend mode */
                $stockId = $this->hlpStock->getCurrentStockId();
            }
        } else {
            $stockId = $this->hlpStock->getStockIdByStoreId($storeId);
        }
        /* customer group ID */
        if (is_null($groupId)) {
            if ($scope != \Magento\Framework\App\Area::AREA_FRONTEND) {
                /* backend mode */
                $custGroup = $this->manGroup->getDefaultGroup();
                $groupId = $custGroup->getId();
            } else {
                /* frontend mode */
                $groupId = $this->session->getCustomerGroupId();
            }
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