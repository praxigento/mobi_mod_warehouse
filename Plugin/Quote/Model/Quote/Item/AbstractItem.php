<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Warehouse\Plugin\Quote\Model\Quote\Item;

use Magento\Catalog\Api\Data\ProductAttributeInterface as EProdAttr;
use Praxigento\Warehouse\Repo\Query\Product\GetPrices as QBGetPrices;

class AbstractItem
{
    /** @var \Praxigento\Warehouse\Api\Helper\Stock */
    private $hlpStock;
    /** @var \Magento\Customer\Api\GroupManagementInterface */
    private $manGroup;
    /** @var \Magento\Customer\Model\Session */
    private $modSession;
    /** @var \Praxigento\Warehouse\Repo\Query\Product\GetPrices */
    private $qbGetPrices;
    /** @var \Magento\Framework\Config\ScopeInterface */
    private $scope;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Config\ScopeInterface $scope,
        \Magento\Customer\Api\GroupManagementInterface $manGroup,
        \Magento\Customer\Model\Session $modSession,
        \Praxigento\Warehouse\Api\Helper\Stock $hlpStock,
        \Praxigento\Warehouse\Repo\Query\Product\GetPrices $qbGetPrices
    ) {
        $this->scope = $scope;
        $this->manGroup = $manGroup;
        $this->modSession = $modSession;
        $this->hlpStock = $hlpStock;
        $this->qbGetPrices = $qbGetPrices;
    }

    /**
     * Add warehouse & group prices to product loaded as quote item.
     *
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $subject
     * @param \Magento\Catalog\Model\Product $result
     * @return \Magento\Catalog\Model\Product
     */
    public function afterGetProduct(
        \Magento\Quote\Model\Quote\Item\AbstractItem $subject,
        \Magento\Catalog\Model\Product $result
    ) {
        $quote = $subject->getQuote();
//        if ($quote) {
//            /* define query params */
//            $storeId = $quote->getStoreId();
//            $stockId = $this->hlpStock->getStockIdByStoreId($storeId);
//            $groupId = $quote->getCustomerGroupId();
//            $prodId = $result->getId();
//
//            /* perform query */
//            $query = $this->qbGetPrices->build();
//            $conn = $query->getConnection();
//            $bind = [
//                QBGetPrices::BND_PROD_ID => $prodId,
//                QBGetPrices::BND_STOCK_ID => $stockId,
//                QBGetPrices::BND_GROUP_ID => $groupId
//            ];
//            $rs = $conn->fetchRow($query, $bind);
//            if (is_array($rs)) {
//                $priceWrhs = $rs[QBGetPrices::A_WRHS_PRICE];
//                $priceGroup = $rs[QBGetPrices::A_WRHS_GROUP_PRICE];
//                if ($priceWrhs) {
//                    $result->setData(EProdAttr::CODE_PRICE, $priceWrhs);
//                }
//                if ($priceGroup) {
//                    $result->setData(EProdAttr::CODE_SPECIAL_PRICE, $priceGroup);
//                }
//            }
//        }
        return $result;
    }
}