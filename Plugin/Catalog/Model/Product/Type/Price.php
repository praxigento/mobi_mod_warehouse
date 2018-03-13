<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Plugin\Catalog\Model\Product\Type;

use Magento\Catalog\Api\Data\ProductAttributeInterface as EProdAttr;

class Price
{
    /**
     * Additional Product attributes. They are used in query builders to get data from DB and in
     * this plugin to replace original prices by warehouse values.
     */
    const A_PRICE_WRHS = 'prxgt_wrhs_price';
    const A_PRICE_WRHS_GROUP = 'prxgt_wrhs_price_group';

    /** @var \Praxigento\Warehouse\Helper\PriceLoader */
    private $hlpPriceLoader;
    /** @var \Praxigento\Warehouse\Api\Helper\Stock */
    private $hlpStock;
    /** @var \Magento\Customer\Model\Session */
    private $session;

    public function __construct(
        \Magento\Customer\Model\Session $session,
        \Praxigento\Warehouse\Api\Helper\Stock $hlpStock,
        \Praxigento\Warehouse\Helper\PriceLoader $hlpPriceLoader
    ) {
        $this->session = $session;
        $this->hlpStock = $hlpStock;
        $this->hlpPriceLoader = $hlpPriceLoader;
    }

    /**
     * Replace product regular price by warehouse price using product attributes (if loaded before)
     * or load warehouse prices with separate query.
     *
     * @param \Magento\Catalog\Model\Product\Type\Price $subject
     * @param \Closure $proceed
     * @param \Magento\Catalog\Model\Product $product
     * @return float
     */
    public function aroundGetPrice(
        \Magento\Catalog\Model\Product\Type\Price $subject,
        \Closure $proceed,
        \Magento\Catalog\Model\Product $product
    ) {
        $result = $proceed($product);
        $priceWrhs = $product->getData(self::A_PRICE_WRHS);
        $priceWrhsGroup = $product->getData(self::A_PRICE_WRHS_GROUP);
        if (is_null($priceWrhs) && is_null($priceWrhsGroup)) {
            $prodId = $product->getId();
            $storeId = $product->getStoreId();
            $stockId = $this->hlpStock->getStockIdByStoreId($storeId);
            $groupId = $this->session->getCustomerGroupId();
            list($priceWrhs, $priceWrhsGroup) = $this->hlpPriceLoader->load($prodId, $stockId, $groupId);
        }
        if ($priceWrhs > 0) {
            $result = $priceWrhs;
            $product->setData(EProdAttr::CODE_PRICE, $priceWrhs);
        }
        if ($priceWrhsGroup > 0) {
            $product->setData(EProdAttr::CODE_SPECIAL_PRICE, $priceWrhsGroup);
        }
        return $result;
    }
}