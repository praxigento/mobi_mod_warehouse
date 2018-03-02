<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Plugin\Catalog\Model\Product\Type;

use Magento\Catalog\Api\Data\ProductAttributeInterface as EProdAttr;
use Praxigento\Warehouse\Config as Cfg;

class Price
{
    const A_PRICE_WRHS = Cfg::A_PROD_PRICE_WRHS;
    const A_PRICE_WRHS_GROUP = Cfg::A_PROD_PRICE_WRHS_GROUP;

    /**
     * Replace product regular price by warehouse group price or warehouse price.
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
        if ($priceWrhs > 0) {
            $result = $priceWrhs;
            $product->setData(EProdAttr::CODE_PRICE, $priceWrhs);
        }
        if ($priceWrhsGroup > 0) {
            $result = $priceWrhsGroup;
            $product->setData(EProdAttr::CODE_SPECIAL_PRICE, $priceWrhsGroup);
        }
        return $result;
    }
}