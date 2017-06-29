<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Plugin\Catalog\Model\Product\Type;

use Magento\Catalog\Api\Data\ProductInterface as EProduct;

class Price
{
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
        $priceWrhs = $product->getData(\Praxigento\Warehouse\Plugin\Catalog\Model\Layer::AS_ATTR_PRICE_WRHS);
        $priceWrhsGroup = $product->getData(\Praxigento\Warehouse\Plugin\Catalog\Model\Layer::AS_ATTR_PRICE_WRHS);
        if ($priceWrhsGroup > 0) {
            $result = $priceWrhsGroup;
            $product->setData(EProduct::PRICE, $priceWrhsGroup);
        } elseif ($priceWrhs > 0) {
            $result = $priceWrhs;
            $product->setData(EProduct::PRICE, $priceWrhs);
        }
        return $result;
    }
}