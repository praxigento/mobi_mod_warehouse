<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Plugin\Sales\Block\Adminhtml\Order\Create\Search\Grid\Renderer;

use Praxigento\Warehouse\Plugin\Catalog\Model\Product\Type\Price as APrice;

/**
 * Replace product price by warehouse group price before rendering.
 */
class Product
{
    public function beforeRender(
        \Magento\Sales\Block\Adminhtml\Order\Create\Search\Grid\Renderer\Product $subject,
        \Magento\Framework\DataObject $row
    )
    {
        $wrhsPrice = $row->getData(APrice::A_PRICE_WRHS_GROUP);
        if ($wrhsPrice) {
            $row->setPrice($wrhsPrice);
        }
        $result = [$row];
        return $result;
    }
}