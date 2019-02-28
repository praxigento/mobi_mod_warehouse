<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Plugin\Magento\Catalog\Model\Product\Type;

use Magento\Catalog\Api\Data\ProductAttributeInterface as EProdAttr;
use Praxigento\Warehouse\Api\Data\Catalog\Product as AWrhsProd;

class Price
{
    /** @var \Praxigento\Warehouse\Helper\PriceLoader */
    private $hlpPriceLoader;

    public function __construct(
        \Praxigento\Warehouse\Helper\PriceLoader $hlpPriceLoader
    ) {
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
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function aroundGetPrice(
        \Magento\Catalog\Model\Product\Type\Price $subject,
        \Closure $proceed,
        \Magento\Catalog\Model\Product $product
    ) {
        $result = $proceed($product);
        /* did warehouse prices loaded before (in model or collection)? */
        $priceWrhs = $product->getData(AWrhsProd::A_PRICE_WRHS);
        $priceWrhsGroup = $product->getData(AWrhsProd::A_PRICE_WRHS_GROUP);
        /* if didn't then load prices separately */
        if (is_null($priceWrhs) && is_null($priceWrhsGroup)) {
            $prodId = $product->getId();
            $storeId = $product->getStoreId();
            list($priceWrhs, $priceWrhsGroup) = $this->hlpPriceLoader->load($prodId, $storeId);
            $product->setData(AWrhsProd::A_PRICE_WRHS, $priceWrhs);
            $product->setData(AWrhsProd::A_PRICE_WRHS_GROUP, $priceWrhsGroup);
        }
        /* use warehouse price instead of regular price */
        if ($priceWrhs > 0) {
            $result = $priceWrhs;
            $product->setData(EProdAttr::CODE_PRICE, $priceWrhs);
        }
        /* use warehouse group price as special price */
        if ($priceWrhsGroup > 0) {
            $product->setData(EProdAttr::CODE_SPECIAL_PRICE, $priceWrhsGroup);
        }
        return $result;
    }
}