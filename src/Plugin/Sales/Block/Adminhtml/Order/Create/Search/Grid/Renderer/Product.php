<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Plugin\Sales\Block\Adminhtml\Order\Create\Search\Grid\Renderer;

use Praxigento\Warehouse\Repo\Query\Catalog\Model\ResourceModel\Product\Collection\Group\Price\Builder as QBPrice;

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
        $wrhsPrice = $row->getData(QBPrice::A_PRICE);
        if ($wrhsPrice) {
            $row->setPrice($wrhsPrice);
        }
        $result = [$row];
        return $result;
    }
}